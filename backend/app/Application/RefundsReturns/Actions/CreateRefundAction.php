<?php

declare(strict_types=1);

namespace App\Application\RefundsReturns\Actions;

use App\Application\Store\Support\AssertManagerStoreAccess;
use App\Domain\Audit\DTOs\AuditLogEntry;
use App\Domain\Audit\Repositories\AuditLogRepositoryInterface;
use App\Domain\Audit\ValueObjects\AuditAction;
use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;
use App\Domain\Payments\DTOs\RefundRequest;
use App\Domain\Payments\Gateways\PaymentGatewayInterface;
use App\Domain\RefundsReturns\Exceptions\RefundDomainException;
use App\Domain\RefundsReturns\Repositories\RefundsReturnsRepositoryInterface;
use App\Domain\RefundsReturns\ValueObjects\RefundType;
use App\Domain\Shared\ErrorCode;
use App\Domain\Shared\Money;
use App\Models\Refund;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateRefundAction
{
    public function __construct(
        private readonly RefundsReturnsRepositoryInterface $refunds,
        private readonly InventoryRepositoryInterface $inventory,
        private readonly PaymentGatewayInterface $gateway,
        private readonly AssertManagerStoreAccess $storeAccess,
        private readonly AuditLogRepositoryInterface $auditLogs,
    ) {}

    /**
     * @param  list<array{sale_line_id: int, quantity: int}>  $lineInputs
     */
    public function execute(
        int $saleId,
        RefundType $type,
        string $reason,
        User $manager,
        array $lineInputs = [],
    ): Refund {
        $sale = $this->refunds->findCompletedSale($saleId);

        if ($sale === null) {
            throw new RefundDomainException(ErrorCode::RefSaleNotFound);
        }

        $this->storeAccess->assertCanAccess($manager, (int) $sale->store_id);

        $userId = (int) $manager->id;
        $alreadyRefunded = $this->refunds->totalRefundedAmount($sale->id);
        $refundableBalance = Money::sub((int) $sale->total, $alreadyRefunded);

        if ($refundableBalance <= 0) {
            throw new RefundDomainException(ErrorCode::RefAlreadyFullyRefunded);
        }

        $refundedQty = $this->refunds->refundedQuantitiesBySaleLine($sale->id);
        $resolvedLines = $this->resolveLines($sale, $type, $lineInputs, $refundedQty);

        $amount = 0;
        foreach ($resolvedLines as $line) {
            $amount = Money::add($amount, $line['amount']);
        }

        if ($type->isFull()) {
            $resolvedLines = $this->scaleLineAmounts($resolvedLines, $amount, $refundableBalance);
            $amount = $refundableBalance;
        } elseif ($amount > $refundableBalance) {
            throw new RefundDomainException(ErrorCode::RefAmountExceedsSale);
        }

        if ($amount <= 0) {
            throw new RefundDomainException(ErrorCode::RefAmountExceedsSale);
        }

        return DB::transaction(function () use ($sale, $type, $reason, $userId, $resolvedLines, $amount): Refund {
            $paymentRef = $this->refundPayments($sale, $amount);

            if ($type->restocks()) {
                $this->inventory->incrementForReturn(
                    $sale->store_id,
                    array_map(
                        static fn (array $line): array => [
                            'product_id' => $line['product_id'],
                            'quantity' => $line['quantity'],
                        ],
                        $resolvedLines,
                    ),
                );
            }

            $refund = $this->refunds->create([
                'sale_id' => $sale->id,
                'store_id' => $sale->store_id,
                'user_id' => $userId,
                'type' => $type->value,
                'reason' => $reason,
                'amount' => $amount,
                'payment_refund_reference' => $paymentRef,
            ], array_map(
                static fn (array $line): array => [
                    'sale_line_id' => $line['sale_line_id'],
                    'quantity' => $line['quantity'],
                    'amount' => $line['amount'],
                    'restocked' => $type->restocks(),
                ],
                $resolvedLines,
            ));

            $isReturn = $type === RefundType::FullReturn || $type === RefundType::PartialReturn;

            $this->auditLogs->append(new AuditLogEntry(
                action: $isReturn ? AuditAction::ReturnCreated : AuditAction::RefundCreated,
                actorUserId: $userId,
                subjectType: 'refund',
                subjectId: (int) $refund->id,
                storeId: (int) $sale->store_id,
                oldValues: null,
                newValues: [
                    'type' => $type->value,
                    'amount' => $amount,
                    'reason' => $reason,
                ],
                metadata: [
                    'sale_id' => $sale->id,
                    'restocked' => $type->restocks(),
                    'lines' => array_map(
                        static fn (array $line): array => [
                            'sale_line_id' => $line['sale_line_id'],
                            'quantity' => $line['quantity'],
                            'amount' => $line['amount'],
                        ],
                        $resolvedLines,
                    ),
                ],
            ));

            return $refund;
        });
    }

    /**
     * @param  list<array{sale_line_id: int, quantity: int}>  $lineInputs
     * @param  array<int, int>  $refundedQty
     * @return list<array{sale_line_id: int, product_id: int, quantity: int, amount: int}>
     */
    private function resolveLines(Sale $sale, RefundType $type, array $lineInputs, array $refundedQty): array
    {
        if ($type->isFull()) {
            $resolved = [];
            foreach ($sale->lines as $saleLine) {
                $remaining = $saleLine->quantity - ($refundedQty[$saleLine->id] ?? 0);
                if ($remaining <= 0) {
                    continue;
                }
                $resolved[] = $this->buildLinePayload($saleLine, $remaining);
            }

            if ($resolved === []) {
                throw new RefundDomainException(ErrorCode::RefAlreadyFullyRefunded);
            }

            return $resolved;
        }

        if ($lineInputs === []) {
            throw new RefundDomainException(ErrorCode::RefReturnQtyInvalid);
        }

        $linesById = $sale->lines->keyBy('id');
        $resolved = [];

        foreach ($lineInputs as $input) {
            /** @var SaleLine|null $saleLine */
            $saleLine = $linesById->get($input['sale_line_id']);

            if ($saleLine === null) {
                throw new RefundDomainException(ErrorCode::RefSaleNotFound);
            }

            $remaining = $saleLine->quantity - ($refundedQty[$saleLine->id] ?? 0);
            $qty = $input['quantity'];

            if ($qty < 1 || $qty > $remaining) {
                throw new RefundDomainException(ErrorCode::RefReturnQtyInvalid);
            }

            $resolved[] = $this->buildLinePayload($saleLine, $qty);
        }

        return $resolved;
    }

    /**
     * @return array{sale_line_id: int, product_id: int, quantity: int, amount: int}
     */
    private function buildLinePayload(SaleLine $saleLine, int $quantity): array
    {
        $lineTotal = (int) $saleLine->line_total;
        $amount = $saleLine->quantity > 0
            ? intdiv($lineTotal * $quantity, $saleLine->quantity)
            : 0;

        return [
            'sale_line_id' => $saleLine->id,
            'product_id' => $saleLine->product_id,
            'quantity' => $quantity,
            'amount' => $amount,
        ];
    }

    /**
     * @param  list<array{sale_line_id: int, product_id: int, quantity: int, amount: int}>  $lines
     * @return list<array{sale_line_id: int, product_id: int, quantity: int, amount: int}>
     */
    private function scaleLineAmounts(array $lines, int $rawTotal, int $targetTotal): array
    {
        if ($rawTotal === 0 || $rawTotal === $targetTotal) {
            return $lines;
        }

        $scaled = [];
        $allocated = 0;
        $lastIndex = count($lines) - 1;

        foreach ($lines as $index => $line) {
            if ($index === $lastIndex) {
                $line['amount'] = Money::sub($targetTotal, $allocated);
                $scaled[] = $line;

                continue;
            }

            $portion = intdiv($line['amount'] * $targetTotal, $rawTotal);
            $line['amount'] = $portion;
            $allocated = Money::add($allocated, $portion);
            $scaled[] = $line;
        }

        return $scaled;
    }

    private function refundPayments(Sale $sale, int $amount): string
    {
        $sale->loadMissing('payments');
        $payment = $sale->payments->first();
        $reference = $payment?->transaction_reference ?? 'sale-'.$sale->id;

        $result = $this->gateway->refund(new RefundRequest(
            transactionReference: $reference,
            amount: $amount,
        ));

        return $result->refundReference;
    }
}
