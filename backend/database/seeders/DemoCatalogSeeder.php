<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $beverages = Category::query()->firstOrCreate(
            ['name' => 'Beverages'],
            ['is_active' => true],
        );
        $snacks = Category::query()->firstOrCreate(
            ['name' => 'Snacks'],
            ['is_active' => true],
        );
        $grocery = Category::query()->firstOrCreate(
            ['name' => 'Grocery'],
            ['is_active' => true],
        );

        /** @var list<array{sku: string, name: string, base_price: int, category_id: int}> $products */
        $products = [
            ['sku' => 'DEMO-BEV-001', 'name' => 'Cola Zero 350ml', 'base_price' => 450, 'category_id' => $beverages->id],
            ['sku' => 'DEMO-BEV-002', 'name' => 'Mineral Water 500ml', 'base_price' => 250, 'category_id' => $beverages->id],
            ['sku' => 'DEMO-SNK-001', 'name' => 'Chocolate Bar', 'base_price' => 399, 'category_id' => $snacks->id],
            ['sku' => 'DEMO-SNK-002', 'name' => 'Salted Chips 100g', 'base_price' => 799, 'category_id' => $snacks->id],
            ['sku' => 'DEMO-GRO-001', 'name' => 'Instant Noodles', 'base_price' => 349, 'category_id' => $grocery->id],
            ['sku' => 'DEMO-GRO-002', 'name' => 'White Bread Loaf', 'base_price' => 699, 'category_id' => $grocery->id],
        ];

        foreach ($products as $row) {
            Product::query()->firstOrCreate(
                ['sku' => $row['sku']],
                [
                    'name' => $row['name'],
                    'base_price' => $row['base_price'],
                    'category_id' => $row['category_id'],
                    'is_active' => true,
                ],
            );
        }
    }
}
