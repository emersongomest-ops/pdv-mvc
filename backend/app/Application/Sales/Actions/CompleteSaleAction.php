<?php

declare(strict_types=1);

namespace App\Application\Sales\Actions;

use App\Application\Sales\Orchestration\DispatchSaleSideEffects;
use App\Application\Sales\Support\SaleCartGuard;
use App\Application\Sales\Support\SalePaymentValidator;
use App\Domain\Inventory\Exceptions\InventoryDomainException;
use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;
use App\Domain\Payments\DTOs\PaymentRequest;
use App\Domain\Payments\DTOs\PendingPaymentOutboxEntry;
use App\Domain\Payments\Gateways\PaymentGatewayInterface;
use App\Domain\Payments\Outbox\PendingPaymentOutboxInterface;
use App\Domain\Payments\Repositories\PaymentsRepositoryInterface;
use App\Domain\Payments\ValueObjects\PaymentLineStatus;
use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Domain\Sales\Events\SaleCompleted;
use App\Domain\Sales\Exceptions\SaleDomainException;
use App\Domain\Sales\Fiscal\FiscalReceiptGeneratorInterface;
use App\Domain\Sales\Repositories\SalesRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\PaymentLine;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CompleteSaleAction
{
    public function __construct(
        private readonly SalesRepositoryInterface $sales,
        private readonly PaymentsRepositoryInterface $payments,
        private readonly PaymentGatewayInterface $gateway,
        private readonly FiscalReceiptGeneratorInterface $fiscalReceipts,
        private readonly InventoryRepositoryInterface $inventory,
        private readonly DispatchSaleSideEffects $sideEffects,
        private readonly SalePaymentValidator $paymentValidator,
        private readonly PendingPaymentOutboxInterface $paymentOutbox,
    ) {}

    /**
     * @param  list<array{
     *     method: string,
     *     amount: int,
     *     cash_received?: int|null,
     *     card?: array{
     *         holder_name: string,
     *         number: string,
     *         exp_month: int,
     *         exp_year: int,
     *         indicated_person_name: string,
     *         belongs_to_indicated_person: bool
     *     }
     * }>  $paymentInputs
     */
    public function execute(
        int $saleId,
        array $paymentInputs,
        int $storeId,
        int $userId,
        int $cashShiftId,
    ): Sale {
        $sale = $this->sales->findById($saleId);

        if ($sale === null) {
            throw new SaleDomainException(ErrorCode::SaleNotFound);
        }

        SaleCartGuard::assertMutable($sale, $storeId, $userId, $cashShiftId);

        if ($sale->lines->isEmpty()) {
            throw new SaleDomainException(ErrorCode::SaleEmptyCart);
        }

        $saleTotal = (int) $sale->total;
        $this->paymentValidator->assertValidForCompletion($paymentInputs, $saleTotal);

        try {
            $completedSale = DB::transaction(function () use ($sale, $paymentInputs, $storeId): Sale {
                $sale->load('lines');

                $recordedPayments = [];

                foreach ($paymentInputs as $paymentInput) {
                    $method = PaymentMethod::from($paymentInput['method']);
                    $amount = (int) $paymentInput['amount'];
                    $cashReceived = array_key_exists('cash_received', $paymentInput)
                        ? ($paymentInput['cash_received'] !== null ? (int) $paymentInput['cash_received'] : null)
                        : null;

                    $result = $this->gateway->charge(new PaymentRequest(
                        method: $method,
                        amount: $amount,
                        cashReceived: $cashReceived,
                    ));

                    if (! $result->success) {
                        throw new SaleDomainException(ErrorCode::SalePaymentMismatch);
                    }

                    $recordedPayments[] = [
                        'method' => $method,
                        'amount' => $amount,
                        'cash_received' => $cashReceived,
                        'change_amount' => $result->changeAmount,
                        'transaction_reference' => $result->transactionReference,
                        'status' => $result->awaitingConfirmation
                            ? PaymentLineStatus::Pending
                            : PaymentLineStatus::Confirmed,
                    ];
                }

                $this->payments->recordForSale($sale->id, $recordedPayments);
                $this->enqueuePendingOutbox($sale->id, $storeId, $recordedPayments);
                $this->inventory->decrementForCompletedSale($storeId, $sale->lines);
                $completedSale = $this->sales->complete($sale);
                $this->fiscalReceipts->generateForSale($completedSale);

                return $this->sales->findById($completedSale->id) ?? $completedSale;
            });
        } catch (SaleDomainException|InventoryDomainException $exception) {
            throw $exception;
        } catch (Throwable) {
            throw new SaleDomainException(ErrorCode::SaleFiscalReceiptFailed);
        }

        $this->sideEffects->dispatch($completedSale, $storeId);

        event(new SaleCompleted(
            saleId: $completedSale->id,
            storeId: $storeId,
            operatorId: $userId,
            totalCents: (int) $completedSale->total,
            customerId: $completedSale->customer_id,
        ));

        return $completedSale;
    }

    /**
     * @param  list<array{
     *     method: PaymentMethod,
     *     amount: int,
     *     cash_received: ?int,
     *     change_amount: ?int,
     *     transaction_reference: ?string,
     *     status: PaymentLineStatus
     * }>  $recordedPayments
     */
    private function enqueuePendingOutbox(int $saleId, int $storeId, array $recordedPayments): void
    {
        foreach ($recordedPayments as $payment) {
            if ($payment['status'] !== PaymentLineStatus::Pending) {
                continue;
            }

            $reference = (string) ($payment['transaction_reference'] ?? '');
            if ($reference === '') {
                continue;
            }

            $line = PaymentLine::query()
                ->where('sale_id', $saleId)
                ->where('transaction_reference', $reference)
                ->first();

            if ($line === null) {
                continue;
            }

            $this->paymentOutbox->push(new PendingPaymentOutboxEntry(
                transactionReference: $reference,
                paymentLineId: (int) $line->id,
                saleId: $saleId,
                storeId: $storeId,
                provider: (string) config('payments.async_settlement.provider', 'stub'),
                attempts: 0,
                enqueuedAt: now()->toIso8601String(),
            ));
        }
    }
}
