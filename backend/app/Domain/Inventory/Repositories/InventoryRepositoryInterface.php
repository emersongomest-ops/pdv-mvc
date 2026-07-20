<?php

declare(strict_types=1);

namespace App\Domain\Inventory\Repositories;

use App\Models\SaleLine;
use App\Models\StoreInventory;
use Illuminate\Support\Collection;

interface InventoryRepositoryInterface
{
    public function findForStoreProduct(int $storeId, int $productId): ?StoreInventory;

    /**
     * Batch inventory rows for a store keyed by product_id.
     *
     * @param  list<int>  $productIds
     * @return Collection<int, StoreInventory>
     */
    public function mapForStoreProducts(int $storeId, array $productIds): Collection;

    /**
     * @return Collection<int, StoreInventory>
     */
    public function listForStore(int $storeId): Collection;

    /**
     * @param  iterable<int, SaleLine>  $lines
     */
    public function decrementForCompletedSale(int $storeId, iterable $lines): void;

    /**
     * @param  iterable<int, array{product_id: int, quantity: int}>  $items
     */
    public function incrementForReturn(int $storeId, iterable $items): void;

    public function adjustQuantity(
        int $storeId,
        int $productId,
        int $newQuantity,
        string $reason,
        int $userId,
    ): StoreInventory;
}
