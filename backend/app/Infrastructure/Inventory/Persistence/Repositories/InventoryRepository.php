<?php

declare(strict_types=1);

namespace App\Infrastructure\Inventory\Persistence\Repositories;

use App\Domain\Inventory\Exceptions\InventoryDomainException;
use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\StockAdjustment;
use App\Models\StoreInventory;
use Illuminate\Support\Collection;

final class InventoryRepository implements InventoryRepositoryInterface
{
    public function findForStoreProduct(int $storeId, int $productId): ?StoreInventory
    {
        return StoreInventory::query()
            ->where('store_id', $storeId)
            ->where('product_id', $productId)
            ->first();
    }

    public function mapForStoreProducts(int $storeId, array $productIds): Collection
    {
        if ($productIds === []) {
            return collect();
        }

        return StoreInventory::query()
            ->where('store_id', $storeId)
            ->whereIn('product_id', $productIds)
            ->get()
            ->keyBy('product_id');
    }

    public function listForStore(int $storeId): Collection
    {
        return StoreInventory::query()
            ->with('product')
            ->where('store_id', $storeId)
            ->orderBy('product_id')
            ->get();
    }

    public function decrementForCompletedSale(int $storeId, iterable $lines): void
    {
        $requestedByProduct = [];

        foreach ($lines as $line) {
            $productId = (int) $line->product_id;
            $requestedByProduct[$productId] = ($requestedByProduct[$productId] ?? 0)
                + (int) $line->quantity;
        }

        if ($requestedByProduct === []) {
            return;
        }

        $inventoryByProduct = StoreInventory::query()
            ->where('store_id', $storeId)
            ->whereIn('product_id', array_keys($requestedByProduct))
            ->lockForUpdate()
            ->get()
            ->keyBy('product_id');

        $updates = [];
        $now = now();

        foreach ($requestedByProduct as $productId => $requestedQuantity) {
            /** @var StoreInventory|null $inventory */
            $inventory = $inventoryByProduct->get($productId);

            if ($inventory === null || ! $inventory->track_stock) {
                continue;
            }

            if ($requestedQuantity > $inventory->quantity) {
                throw new InventoryDomainException(ErrorCode::InvInsufficientStock);
            }

            $updates[] = [
                'id' => $inventory->id,
                'store_id' => $storeId,
                'product_id' => $productId,
                'quantity' => $inventory->quantity - $requestedQuantity,
                'track_stock' => true,
                'created_at' => $inventory->created_at,
                'updated_at' => $now,
            ];
        }

        if ($updates !== []) {
            StoreInventory::query()->upsert(
                $updates,
                ['id'],
                ['quantity', 'updated_at'],
            );
        }
    }

    public function incrementForReturn(int $storeId, iterable $items): void
    {
        foreach ($items as $item) {
            $inventory = StoreInventory::query()
                ->where('store_id', $storeId)
                ->where('product_id', $item['product_id'])
                ->lockForUpdate()
                ->first();

            if ($inventory === null || ! $inventory->track_stock) {
                continue;
            }

            $inventory->update([
                'quantity' => $inventory->quantity + $item['quantity'],
            ]);
        }
    }

    public function adjustQuantity(
        int $storeId,
        int $productId,
        int $newQuantity,
        string $reason,
        int $userId,
    ): StoreInventory {
        if (trim($reason) === '') {
            throw new InventoryDomainException(ErrorCode::InvAdjustmentReasonRequired);
        }

        $inventory = StoreInventory::query()->firstOrNew([
            'store_id' => $storeId,
            'product_id' => $productId,
        ]);

        $previousQuantity = $inventory->exists ? $inventory->quantity : 0;

        $inventory->fill([
            'quantity' => $newQuantity,
            'track_stock' => true,
        ]);
        $inventory->save();

        StockAdjustment::query()->create([
            'store_id' => $storeId,
            'product_id' => $productId,
            'user_id' => $userId,
            'previous_quantity' => $previousQuantity,
            'new_quantity' => $newQuantity,
            'reason' => $reason,
        ]);

        return $inventory->fresh(['product']) ?? $inventory;
    }
}
