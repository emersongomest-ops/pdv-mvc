<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Models\Product;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class OperationalCatalogTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    public function test_operator_lists_active_products_with_stock_for_current_store(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorAtStore($operator, $store);

        $active = Product::factory()->create(['name' => 'Cola Zero', 'sku' => 'BEV-001']);
        Product::factory()->inactive()->create(['name' => 'Discontinued']);

        StoreInventory::factory()->create([
            'store_id' => $store->id,
            'product_id' => $active->id,
            'quantity' => 12,
        ]);

        $response = $this->getJson('/api/operational/catalog/products');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.sku', 'BEV-001')
            ->assertJsonPath('data.products.0.available_quantity', 12)
            ->assertJsonPath('data.products.0.track_stock', true);
    }

    public function test_operator_can_search_products_by_name_or_sku(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorAtStore($operator, $store);

        Product::factory()->create(['name' => 'Chocolate Bar', 'sku' => 'SNK-100']);
        Product::factory()->create(['name' => 'Water', 'sku' => 'BEV-200']);

        $this->getJson('/api/operational/catalog/products?search=choco')
            ->assertOk()
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.name', 'Chocolate Bar');
    }

    public function test_listing_products_requires_store_context(): void
    {
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)
            ->getJson('/api/operational/catalog/products')
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'AUTH_STORE_CONTEXT_REQUIRED');
    }

    public function test_listing_products_batches_inventory_lookups(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorAtStore($operator, $store);

        $products = Product::factory()->count(5)->create();
        foreach ($products as $product) {
            StoreInventory::factory()->create([
                'store_id' => $store->id,
                'product_id' => $product->id,
                'quantity' => 3,
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->getJson('/api/operational/catalog/products')
            ->assertOk()
            ->assertJsonCount(5, 'data.products');

        $inventoryQueries = collect(DB::getQueryLog())
            ->filter(static fn (array $query): bool => str_contains($query['query'], 'store_inventories'))
            ->count();

        $this->assertLessThanOrEqual(
            1,
            $inventoryQueries,
            'Operational catalog must not N+1 store_inventories (expected ≤1 query).',
        );
    }

    public function test_operator_can_paginate_products_with_cursor(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorAtStore($operator, $store);

        Product::factory()->create(['name' => 'Alpha', 'sku' => 'A-1']);
        Product::factory()->create(['name' => 'Bravo', 'sku' => 'B-1']);
        Product::factory()->create(['name' => 'Charlie', 'sku' => 'C-1']);

        $first = $this->getJson('/api/operational/catalog/products?per_page=2');
        $first
            ->assertOk()
            ->assertJsonCount(2, 'data.products')
            ->assertJsonPath('data.products.0.name', 'Alpha')
            ->assertJsonPath('data.products.1.name', 'Bravo');

        $cursor = $first->json('meta.next_cursor');
        $this->assertNotNull($cursor);

        $second = $this->getJson('/api/operational/catalog/products?per_page=2&cursor='.urlencode((string) $cursor));
        $second
            ->assertOk()
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.name', 'Charlie')
            ->assertJsonPath('meta.next_cursor', null);
    }

    public function test_invalid_catalog_cursor_returns_validation_error(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorAtStore($operator, $store);

        $this->getJson('/api/operational/catalog/products?cursor=not-a-cursor')
            ->assertStatus(422);
    }
}
