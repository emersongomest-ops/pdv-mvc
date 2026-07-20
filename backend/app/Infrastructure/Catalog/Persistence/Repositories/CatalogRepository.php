<?php

declare(strict_types=1);

namespace App\Infrastructure\Catalog\Persistence\Repositories;

use App\Domain\Catalog\Repositories\CatalogRepositoryInterface;
use App\Models\Category;
use App\Models\Product;
use App\Models\SaleLine;
use Illuminate\Support\Collection;

final class CatalogRepository implements CatalogRepositoryInterface
{
    public function listCategories(): Collection
    {
        return Category::query()->orderBy('name')->get();
    }

    public function findCategoryById(int $id): ?Category
    {
        return Category::query()->find($id);
    }

    public function categoryNameExists(string $name, ?int $exceptCategoryId = null): bool
    {
        $query = Category::query()->where('name', $name);

        if ($exceptCategoryId !== null) {
            $query->whereKeyNot($exceptCategoryId);
        }

        return $query->exists();
    }

    public function createCategory(string $name, bool $isActive = true): Category
    {
        return Category::query()->create([
            'name' => $name,
            'is_active' => $isActive,
        ]);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->fresh() ?? $category;
    }

    public function deleteCategory(Category $category): void
    {
        $category->delete();
    }

    public function categoryHasProducts(int $categoryId): bool
    {
        return Product::query()->where('category_id', $categoryId)->exists();
    }

    public function listProducts(?int $categoryId = null, ?bool $isActive = null, ?string $search = null): Collection
    {
        $query = Product::query()->with('category')->orderBy('name');

        if ($categoryId !== null) {
            $query->where('category_id', $categoryId);
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        if ($search !== null && $search !== '') {
            $needle = '%'.mb_strtolower($search).'%';
            $query->where(function ($builder) use ($needle): void {
                $builder
                    ->whereRaw('LOWER(name) LIKE ?', [$needle])
                    ->orWhereRaw('LOWER(sku) LIKE ?', [$needle]);
            });
        }

        return $query->get();
    }

    public function listProductsPage(
        ?int $categoryId,
        ?bool $isActive,
        ?string $search,
        ?string $cursor,
        int $perPage,
    ): array {
        $query = Product::query()
            ->with('category')
            ->orderBy('name')
            ->orderBy('id');

        if ($categoryId !== null) {
            $query->where('category_id', $categoryId);
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        if ($search !== null && $search !== '') {
            $needle = '%'.mb_strtolower($search).'%';
            $query->where(function ($builder) use ($needle): void {
                $builder
                    ->whereRaw('LOWER(name) LIKE ?', [$needle])
                    ->orWhereRaw('LOWER(sku) LIKE ?', [$needle]);
            });
        }

        if ($cursor !== null && $cursor !== '') {
            [$cursorName, $cursorId] = $this->decodeProductCursor($cursor);
            $query->where(function ($builder) use ($cursorName, $cursorId): void {
                $builder
                    ->where('name', '>', $cursorName)
                    ->orWhere(function ($inner) use ($cursorName, $cursorId): void {
                        $inner->where('name', $cursorName)->where('id', '>', $cursorId);
                    });
            });
        }

        /** @var Collection<int, Product> $rows */
        $rows = $query->limit($perPage + 1)->get();

        $nextCursor = null;
        if ($rows->count() > $perPage) {
            $rows = $rows->take($perPage)->values();
            $last = $rows->last();
            if ($last !== null) {
                $nextCursor = $this->encodeProductCursor($last);
            }
        }

        return [
            'items' => $rows->values(),
            'next_cursor' => $nextCursor,
        ];
    }

    public function listActiveProductsPage(
        ?int $categoryId,
        ?string $search,
        ?string $cursor,
        int $perPage,
    ): array {
        return $this->listProductsPage($categoryId, true, $search, $cursor, $perPage);
    }

    private function encodeProductCursor(Product $product): string
    {
        $payload = json_encode([
            'n' => (string) $product->name,
            'i' => (int) $product->id,
        ], JSON_THROW_ON_ERROR);

        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }

    /**
     * @return array{0: string, 1: int}
     */
    private function decodeProductCursor(string $cursor): array
    {
        $decoded = base64_decode(strtr($cursor, '-_', '+/'), true);
        if ($decoded === false) {
            throw new \InvalidArgumentException('Invalid product cursor.');
        }

        try {
            /** @var array{n?: mixed, i?: mixed} $payload */
            $payload = json_decode($decoded, true, 8, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new \InvalidArgumentException('Invalid product cursor.');
        }

        $name = $payload['n'] ?? null;
        $id = $payload['i'] ?? null;
        if (! is_string($name) || ! is_int($id) || $id < 1) {
            throw new \InvalidArgumentException('Invalid product cursor.');
        }

        return [$name, $id];
    }

    public function findProductById(int $id): ?Product
    {
        return Product::query()->with('category')->find($id);
    }

    public function skuExists(string $sku, ?int $exceptProductId = null): bool
    {
        $query = Product::query()->where('sku', $sku);

        if ($exceptProductId !== null) {
            $query->whereKeyNot($exceptProductId);
        }

        return $query->exists();
    }

    public function createProduct(array $data): Product
    {
        return Product::query()->create([
            'sku' => $data['sku'],
            'name' => $data['name'],
            'base_price' => $data['base_price'],
            'is_active' => $data['is_active'] ?? true,
            'category_id' => $data['category_id'] ?? null,
        ]);
    }

    public function updateProduct(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh(['category']) ?? $product;
    }

    public function deleteProduct(Product $product): void
    {
        $product->delete();
    }

    public function productHasSaleLines(int $productId): bool
    {
        return SaleLine::query()->where('product_id', $productId)->exists();
    }
}
