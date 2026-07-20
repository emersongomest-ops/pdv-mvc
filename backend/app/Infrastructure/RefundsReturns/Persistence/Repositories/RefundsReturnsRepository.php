<?php

declare(strict_types=1);

namespace App\Infrastructure\RefundsReturns\Persistence\Repositories;

use App\Domain\RefundsReturns\Repositories\RefundsReturnsRepositoryInterface;
use App\Domain\Sales\ValueObjects\SaleStatus;
use App\Models\Refund;
use App\Models\RefundLine;
use App\Models\Sale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class RefundsReturnsRepository implements RefundsReturnsRepositoryInterface
{
    public function findCompletedSale(int $saleId): ?Sale
    {
        return Sale::query()
            ->with(['lines', 'payments'])
            ->whereKey($saleId)
            ->where('status', SaleStatus::Completed)
            ->first();
    }

    public function totalRefundedAmount(int $saleId): int
    {
        return (int) Refund::query()->where('sale_id', $saleId)->sum('amount');
    }

    public function refundedQuantitiesBySaleLine(int $saleId): array
    {
        $rows = RefundLine::query()
            ->selectRaw('sale_line_id, SUM(quantity) as qty')
            ->whereHas('refund', fn ($q) => $q->where('sale_id', $saleId))
            ->groupBy('sale_line_id')
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row->sale_line_id] = (int) $row->qty;
        }

        return $map;
    }

    public function create(array $header, array $lines): Refund
    {
        return DB::transaction(function () use ($header, $lines): Refund {
            $refund = Refund::query()->create($header);

            foreach ($lines as $line) {
                RefundLine::query()->create([
                    'refund_id' => $refund->id,
                    'sale_line_id' => $line['sale_line_id'],
                    'quantity' => $line['quantity'],
                    'amount' => $line['amount'],
                    'restocked' => $line['restocked'],
                ]);
            }

            return $refund->fresh(['lines.saleLine', 'user']) ?? $refund;
        });
    }

    public function listForSale(int $saleId): Collection
    {
        return Refund::query()
            ->with(['lines.saleLine', 'user'])
            ->where('sale_id', $saleId)
            ->orderByDesc('id')
            ->get();
    }
}
