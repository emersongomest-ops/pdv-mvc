<?php

declare(strict_types=1);

namespace App\Infrastructure\Sales\Persistence\Repositories;

use App\Domain\Promotions\Support\PromotionDiscountCalculator;
use App\Domain\Sales\DTOs\AdminSaleFilters;
use App\Domain\Sales\Repositories\SalesRepositoryInterface;
use App\Domain\Sales\ValueObjects\SaleStatus;
use App\Domain\Shared\Money;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\SalePromotion;
use Illuminate\Support\Collection;

final class SalesRepository implements SalesRepositoryInterface
{
    public function findById(int $id): ?Sale
    {
        return Sale::query()->with('lines')->find($id);
    }

    public function createInProgress(int $storeId, int $userId, int $cashShiftId): Sale
    {
        return Sale::query()->create([
            'store_id' => $storeId,
            'user_id' => $userId,
            'cash_shift_id' => $cashShiftId,
            'status' => SaleStatus::InProgress,
            'subtotal' => 0,
            'discount_total' => 0,
            'total' => 0,
        ]);
    }

    public function listForAdmin(AdminSaleFilters $filters, array $allowedStoreIds): Collection
    {
        if ($allowedStoreIds === []) {
            return collect();
        }

        $query = Sale::query()
            ->with(['payments', 'operator:id,name', 'store:id,name,code', 'customer:id,name'])
            ->whereIn('store_id', $allowedStoreIds)
            ->orderByDesc('completed_at')
            ->orderByDesc('id');

        if ($filters->status !== null) {
            $query->where('status', $filters->status);
        }

        if ($filters->storeId !== null) {
            $query->where('store_id', $filters->storeId);
        }

        if ($filters->operatorId !== null) {
            $query->where('user_id', $filters->operatorId);
        }

        if ($filters->customerId !== null) {
            $query->where('customer_id', $filters->customerId);
        }

        if ($filters->fromDate !== null) {
            $query->whereDate('completed_at', '>=', $filters->fromDate);
        }

        if ($filters->toDate !== null) {
            $query->whereDate('completed_at', '<=', $filters->toDate);
        }

        if ($filters->paymentMethod !== null) {
            $method = $filters->paymentMethod;
            $query->whereHas(
                'payments',
                static fn ($payments) => $payments->where('method', $method),
            );
        }

        return $query->limit(200)->get();
    }

    public function listHeldForShift(int $storeId, int $userId, int $cashShiftId): Collection
    {
        return Sale::query()
            ->with('lines')
            ->where('store_id', $storeId)
            ->where('user_id', $userId)
            ->where('cash_shift_id', $cashShiftId)
            ->where('status', SaleStatus::Held)
            ->orderByDesc('held_at')
            ->get();
    }

    public function hold(Sale $sale, ?string $label): Sale
    {
        $sale->update([
            'status' => SaleStatus::Held,
            'hold_label' => $label,
            'held_at' => now(),
        ]);

        return $sale->fresh(['lines']);
    }

    public function resume(Sale $sale): Sale
    {
        $sale->update([
            'status' => SaleStatus::InProgress,
            'hold_label' => null,
            'held_at' => null,
        ]);

        return $sale->fresh(['lines']);
    }

    public function findLineById(int $saleId, int $lineId): ?SaleLine
    {
        return SaleLine::query()
            ->where('sale_id', $saleId)
            ->whereKey($lineId)
            ->first();
    }

    public function findLineByProduct(int $saleId, int $productId): ?SaleLine
    {
        return SaleLine::query()
            ->where('sale_id', $saleId)
            ->where('product_id', $productId)
            ->first();
    }

    public function addLine(Sale $sale, int $productId, int $unitPriceCents, int $quantity): SaleLine
    {
        $existing = $this->findLineByProduct($sale->id, $productId);

        if ($existing !== null) {
            return $this->updateLineQuantity($existing, $existing->quantity + $quantity);
        }

        $lineTotal = Money::mulQty($unitPriceCents, $quantity);

        $line = SaleLine::query()->create([
            'sale_id' => $sale->id,
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_price' => $unitPriceCents,
            'line_discount' => 0,
            'line_total' => $lineTotal,
        ]);

        return $this->recalculateTotals($sale)->lines()->whereKey($line->id)->first() ?? $line->fresh();
    }

    public function updateLineQuantity(SaleLine $line, int $quantity): SaleLine
    {
        $lineTotal = Money::mulQty((int) $line->unit_price, $quantity);

        $line->update([
            'quantity' => $quantity,
            'line_total' => $lineTotal,
        ]);

        $sale = $line->sale;

        return $this->recalculateTotals($sale)->lines()->whereKey($line->id)->first() ?? $line->fresh();
    }

    public function removeLine(SaleLine $line): void
    {
        $sale = $line->sale;
        $line->delete();
        $this->recalculateTotals($sale);
    }

    public function recalculateTotals(Sale $sale): Sale
    {
        $sale->load(['lines', 'salePromotions.promotion']);

        $subtotal = 0;
        $lineDiscountTotal = 0;

        foreach ($sale->lines as $line) {
            $subtotal = Money::add($subtotal, (int) $line->line_total);
            $lineDiscountTotal = Money::add($lineDiscountTotal, (int) $line->line_discount);
        }

        $promotions = $sale->salePromotions
            ->map(fn (SalePromotion $applied) => $applied->promotion)
            ->filter()
            ->values();

        $promoResult = PromotionDiscountCalculator::calculate($subtotal, $promotions);

        foreach ($sale->salePromotions as $applied) {
            $amount = $promoResult['amounts'][$applied->promotion_id] ?? 0;
            if ((int) $applied->discount_amount !== $amount) {
                $applied->update(['discount_amount' => $amount]);
            }
        }

        $discountTotal = Money::add($lineDiscountTotal, $promoResult['discount_total']);
        $total = Money::sub($subtotal, $discountTotal);

        if ($total < 0) {
            $total = 0;
        }

        $sale->update([
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'total' => $total,
        ]);

        return $sale->fresh(['lines', 'salePromotions.promotion', 'customer']);
    }

    public function attachCustomer(Sale $sale, int $customerId): Sale
    {
        $sale->update([
            'customer_id' => $customerId,
        ]);

        return $sale->fresh(['lines', 'customer']);
    }

    public function complete(Sale $sale): Sale
    {
        $sale->update([
            'status' => SaleStatus::Completed,
            'completed_at' => now(),
        ]);

        return $sale->fresh(['lines', 'payments', 'fiscalReceipt', 'customer']);
    }
}
