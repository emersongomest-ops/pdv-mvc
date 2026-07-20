<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_product_with_base_price_and_category(): void
    {
        $manager = User::factory()->manager()->create();
        $category = Category::factory()->create(['name' => 'Grocery']);

        $response = $this->actingAs($manager)->postJson('/api/admin/catalog/products', [
            'sku' => 'SKU-100',
            'name' => 'Organic Milk',
            'base_price' => 7.49,
            'category_id' => $category->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.product.sku', 'SKU-100')
            ->assertJsonPath('data.product.base_price', '7.49')
            ->assertJsonPath('data.product.category_name', 'Grocery');

        $this->assertDatabaseHas('products', [
            'sku' => 'SKU-100',
            'base_price' => 749,
            'category_id' => $category->id,
        ]);
    }

    public function test_manager_can_update_product_base_price(): void
    {
        $manager = User::factory()->manager()->create();
        $product = Product::factory()->create(['base_price' => 1000]);

        $this->actingAs($manager)->patchJson("/api/admin/catalog/products/{$product->id}", [
            'base_price' => 12.50,
        ])
            ->assertOk()
            ->assertJsonPath('data.product.base_price', '12.50');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'base_price' => 1250,
        ]);
    }

    public function test_manager_can_filter_products_by_category_and_active_flag(): void
    {
        $manager = User::factory()->manager()->create();
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();

        Product::factory()->create(['category_id' => $categoryA->id, 'is_active' => true]);
        Product::factory()->inactive()->create(['category_id' => $categoryA->id]);
        Product::factory()->create(['category_id' => $categoryB->id]);

        $this->actingAs($manager)
            ->getJson('/api/admin/catalog/products?category_id='.$categoryA->id.'&is_active=1')
            ->assertOk()
            ->assertJsonCount(1, 'data.products');
    }

    public function test_manager_can_paginate_products_with_cursor(): void
    {
        $manager = User::factory()->manager()->create();

        Product::factory()->create(['name' => 'Alpha', 'sku' => 'A-1']);
        Product::factory()->create(['name' => 'Bravo', 'sku' => 'B-1']);
        Product::factory()->create(['name' => 'Charlie', 'sku' => 'C-1']);

        $first = $this->actingAs($manager)->getJson('/api/admin/catalog/products?per_page=2');
        $first
            ->assertOk()
            ->assertJsonCount(2, 'data.products')
            ->assertJsonPath('data.products.0.name', 'Alpha')
            ->assertJsonPath('data.products.1.name', 'Bravo');

        $cursor = $first->json('meta.next_cursor');
        $this->assertNotNull($cursor);

        $second = $this->actingAs($manager)->getJson(
            '/api/admin/catalog/products?per_page=2&cursor='.urlencode((string) $cursor),
        );
        $second
            ->assertOk()
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.name', 'Charlie')
            ->assertJsonPath('meta.next_cursor', null);
    }

    public function test_invalid_admin_catalog_cursor_returns_validation_error(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/catalog/products?per_page=2&cursor=not-a-cursor')
            ->assertStatus(422);
    }

    public function test_admin_products_without_per_page_omits_meta_cursor(): void
    {
        $manager = User::factory()->manager()->create();
        Product::factory()->count(2)->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/catalog/products')
            ->assertOk()
            ->assertJsonCount(2, 'data.products')
            ->assertJsonMissingPath('meta.next_cursor');
    }

    public function test_duplicate_sku_returns_cat_sku_duplicate(): void
    {
        $manager = User::factory()->manager()->create();
        Product::factory()->create(['sku' => 'SKU-DUP']);

        $this->actingAs($manager)->postJson('/api/admin/catalog/products', [
            'sku' => 'SKU-DUP',
            'name' => 'Another Product',
            'base_price' => 5.00,
        ])
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'CAT_SKU_DUPLICATE');
    }

    public function test_manager_can_delete_unused_product(): void
    {
        $manager = User::factory()->manager()->create();
        $product = Product::factory()->create();

        $this->actingAs($manager)->deleteJson("/api/admin/catalog/products/{$product->id}")
            ->assertOk();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_delete_product_with_sale_lines_returns_cat_product_in_use(): void
    {
        $manager = User::factory()->manager()->create();
        $product = Product::factory()->create();
        $sale = Sale::factory()->create();

        SaleLine::query()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 1000,
            'line_discount' => 0,
            'line_total' => 1000,
        ]);

        $this->actingAs($manager)->deleteJson("/api/admin/catalog/products/{$product->id}")
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'CAT_PRODUCT_IN_USE');
    }
}
