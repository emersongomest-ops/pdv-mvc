<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Repositories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;

interface CatalogRepositoryInterface
{
    /**
     * @return Collection<int, Category>
     */
    public function listCategories(): Collection;

    public function findCategoryById(int $id): ?Category;

    public function categoryNameExists(string $name, ?int $exceptCategoryId = null): bool;

    public function createCategory(string $name, bool $isActive = true): Category;

    /**
     * @param  array{name?: string, is_active?: bool}  $data
     */
    public function updateCategory(Category $category, array $data): Category;

    public function deleteCategory(Category $category): void;

    public function categoryHasProducts(int $categoryId): bool;

    /**
     * @return Collection<int, Product>
     */
    public function listProducts(?int $categoryId = null, ?bool $isActive = null, ?string $search = null): Collection;

    /**
     * Keyset page ordered by name, id.
     *
     * @return array{items: Collection<int, Product>, next_cursor: string|null}
     */
    public function listProductsPage(
        ?int $categoryId,
        ?bool $isActive,
        ?string $search,
        ?string $cursor,
        int $perPage,
    ): array;

    /**
     * Keyset page of active products (POS).
     *
     * @return array{items: Collection<int, Product>, next_cursor: string|null}
     */
    public function listActiveProductsPage(
        ?int $categoryId,
        ?string $search,
        ?string $cursor,
        int $perPage,
    ): array;

    public function findProductById(int $id): ?Product;

    public function skuExists(string $sku, ?int $exceptProductId = null): bool;

    /**
     * @param array{
     *     sku: string,
     *     name: string,
     *     base_price: int,
     *     is_active?: bool,
     *     category_id?: int|null
     * } $data
     */
    public function createProduct(array $data): Product;

    /**
     * @param array{
     *     sku?: string,
     *     name?: string,
     *     base_price?: int,
     *     is_active?: bool,
     *     category_id?: int|null
     * } $data
     */
    public function updateProduct(Product $product, array $data): Product;

    public function deleteProduct(Product $product): void;

    public function productHasSaleLines(int $productId): bool;
}
