<?php

declare(strict_types=1);

namespace App\Application\Catalog\Actions;

use App\Domain\Catalog\Repositories\CatalogRepositoryInterface;
use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;
use App\Domain\Shared\Money;
use App\Models\Product;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class ListOperationalProductsAction
{
    public function __construct(
        private readonly CatalogRepositoryInterface $catalog,
        private readonly InventoryRepositoryInterface $inventory,
    ) {}

    /**
     * Active products for POS with stock for the current store (cursor page).
     *
     * Inventory is loaded in one query for the page only — O(page) time/space.
     *
     * @return array{products: list<array<string, mixed>>, next_cursor: string|null}
     */
    public function execute(
        int $storeId,
        ?int $categoryId = null,
        ?string $search = null,
        ?string $cursor = null,
        int $perPage = 50,
    ): array {
        try {
            $page = $this->catalog->listActiveProductsPage($categoryId, $search, $cursor, $perPage);
        } catch (InvalidArgumentException $e) {
            throw $e;
        }

        /** @var Collection<int, Product> $products */
        $products = $page['items'];
        $productIds = $products->map(static fn (Product $product): int => (int) $product->id)->all();
        $stockByProduct = $this->inventory->mapForStoreProducts($storeId, $productIds);

        $mapped = $products->map(function (Product $product) use ($stockByProduct): array {
            $stock = $stockByProduct->get($product->id);

            return [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'base_price' => Money::toDecimalString((int) $product->base_price),
                'track_stock' => $stock?->track_stock ?? false,
                'available_quantity' => $stock !== null && $stock->track_stock
                    ? $stock->quantity
                    : null,
            ];
        })->values()->all();

        return [
            'products' => $mapped,
            'next_cursor' => $page['next_cursor'],
        ];
    }
}
