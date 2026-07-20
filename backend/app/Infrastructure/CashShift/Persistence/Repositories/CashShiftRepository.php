<?php

declare(strict_types=1);

namespace App\Infrastructure\CashShift\Persistence\Repositories;

use App\Domain\CashShift\DTOs\ShiftClosingReport;
use App\Domain\CashShift\Repositories\CashShiftRepositoryInterface;
use App\Domain\CashShift\ValueObjects\CashShiftStatus;
use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Domain\Sales\ValueObjects\SaleStatus;
use App\Models\CashShift;
use App\Models\PaymentLine;
use App\Models\Sale;
use Illuminate\Support\Collection;

final class CashShiftRepository implements CashShiftRepositoryInterface
{
    public function findOpenForUser(int $userId): ?CashShift
    {
        return CashShift::query()
            ->where('user_id', $userId)
            ->where('status', CashShiftStatus::Open)
            ->first();
    }

    public function findOpenForUserAtStore(int $userId, int $storeId): ?CashShift
    {
        return CashShift::query()
            ->where('user_id', $userId)
            ->where('store_id', $storeId)
            ->where('status', CashShiftStatus::Open)
            ->first();
    }

    public function findById(int $id): ?CashShift
    {
        return CashShift::query()->with(['store', 'operator'])->find($id);
    }

    public function createOpen(int $storeId, int $userId, int $openingCashAmountCents): CashShift
    {
        return CashShift::query()->create([
            'store_id' => $storeId,
            'user_id' => $userId,
            'status' => CashShiftStatus::Open,
            'opening_cash_amount' => $openingCashAmountCents,
            'opened_at' => now(),
        ]);
    }

    public function close(CashShift $shift, ?int $closingCashAmountCents): CashShift
    {
        $shift->update([
            'status' => CashShiftStatus::Closed,
            'closing_cash_amount' => $closingCashAmountCents,
            'closed_at' => now(),
        ]);

        return $shift->fresh(['store', 'operator']);
    }

    public function reopen(CashShift $shift): CashShift
    {
        $shift->update([
            'status' => CashShiftStatus::Open,
            'closing_cash_amount' => null,
            'closed_at' => null,
        ]);

        return $shift->fresh(['store', 'operator']);
    }

    public function buildClosingReport(CashShift $shift): ShiftClosingReport
    {
        $shift->loadMissing(['store', 'operator']);

        $sales = Sale::query()
            ->where('cash_shift_id', $shift->id)
            ->where('status', SaleStatus::Completed)
            ->get(['id', 'total']);

        $salesCount = $sales->count();
        $salesTotalCents = (int) $sales->sum('total');
        $saleIds = $sales->pluck('id')->all();

        $totalsByMethod = [];
        $cashPaymentsCents = 0;

        if ($saleIds !== []) {
            $rows = PaymentLine::query()
                ->whereIn('sale_id', $saleIds)
                ->selectRaw('method, SUM(amount) as total_cents')
                ->groupBy('method')
                ->get();

            foreach ($rows as $row) {
                $method = $row->method instanceof PaymentMethod
                    ? $row->method->value
                    : (string) $row->method;
                $amount = (int) $row->total_cents;
                $totalsByMethod[] = [
                    'method' => $method,
                    'amount_cents' => $amount,
                ];
                if ($method === PaymentMethod::Cash->value) {
                    $cashPaymentsCents = $amount;
                }
            }

            usort(
                $totalsByMethod,
                static fn (array $a, array $b): int => strcmp($a['method'], $b['method']),
            );
        }

        $opening = (int) $shift->opening_cash_amount;
        $expected = $opening + $cashPaymentsCents;
        $closing = $shift->closing_cash_amount !== null ? (int) $shift->closing_cash_amount : null;
        $variance = $closing !== null ? $closing - $expected : null;

        return new ShiftClosingReport(
            shiftId: $shift->id,
            storeId: $shift->store_id,
            operatorId: $shift->user_id,
            salesCount: $salesCount,
            salesTotalCents: $salesTotalCents,
            totalsByPaymentMethod: $totalsByMethod,
            openingCashCents: $opening,
            expectedCashCents: $expected,
            closingCashCents: $closing,
            cashVarianceCents: $variance,
            openedAt: $shift->opened_at?->toIso8601String(),
            closedAt: $shift->closed_at?->toIso8601String(),
            status: $shift->status->value,
            operatorName: $shift->operator?->name,
            storeCode: $shift->store?->code,
        );
    }

    public function listForStore(int $storeId, int $limit = 50): Collection
    {
        return CashShift::query()
            ->with(['operator:id,name', 'store:id,code,name'])
            ->where('store_id', $storeId)
            ->orderByDesc('opened_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }
}
