<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Store;
use App\Models\StoreInventory;
use Illuminate\Database\Seeder;
use RuntimeException;

class DemoInventorySeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::query()->where('code', 'MAIN')->first();

        if ($store === null) {
            throw new RuntimeException('Demo store MAIN missing — run DemoStoreSeeder first.');
        }

        $skus = [
            'DEMO-BEV-001',
            'DEMO-BEV-002',
            'DEMO-SNK-001',
            'DEMO-SNK-002',
            'DEMO-GRO-001',
            'DEMO-GRO-002',
        ];

        $products = Product::query()->whereIn('sku', $skus)->get();

        if ($products->count() !== count($skus)) {
            throw new RuntimeException('Demo catalog products missing — run DemoCatalogSeeder first.');
        }

        $quantities = [
            'DEMO-BEV-001' => 80,
            'DEMO-BEV-002' => 120,
            'DEMO-SNK-001' => 60,
            'DEMO-SNK-002' => 45,
            'DEMO-GRO-001' => 90,
            'DEMO-GRO-002' => 35,
        ];

        foreach ($products as $product) {
            StoreInventory::query()->firstOrCreate(
                [
                    'store_id' => $store->id,
                    'product_id' => $product->id,
                ],
                [
                    'quantity' => $quantities[$product->sku] ?? 50,
                    'track_stock' => true,
                ],
            );
        }
    }
}
