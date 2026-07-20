<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Domain\Shared\Money;
use App\Models\Category;
use App\Models\Product;

final class CatalogResource
{
    /**
     * @return array<string, mixed>
     */
    public static function categoryToArray(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'is_active' => $category->is_active,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function productToArray(Product $product): array
    {
        $product->loadMissing('category');

        return [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'category_id' => $product->category_id,
            'category_name' => $product->category?->name,
            'base_price' => Money::toDecimalString((int) $product->base_price),
            'is_active' => $product->is_active,
        ];
    }
}
