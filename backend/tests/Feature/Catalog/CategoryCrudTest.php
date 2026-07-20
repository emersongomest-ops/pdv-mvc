<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_list_update_and_delete_category(): void
    {
        $manager = User::factory()->manager()->create();

        $createResponse = $this->actingAs($manager)->postJson('/api/admin/catalog/categories', [
            'name' => 'Beverages',
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('data.category.name', 'Beverages')
            ->assertJsonPath('data.category.is_active', true);

        $categoryId = (int) $createResponse->json('data.category.id');

        $this->actingAs($manager)->getJson('/api/admin/catalog/categories')
            ->assertOk()
            ->assertJsonCount(1, 'data.categories');

        $this->actingAs($manager)->patchJson("/api/admin/catalog/categories/{$categoryId}", [
            'name' => 'Cold Beverages',
            'is_active' => false,
        ])
            ->assertOk()
            ->assertJsonPath('data.category.name', 'Cold Beverages')
            ->assertJsonPath('data.category.is_active', false);

        $this->actingAs($manager)->deleteJson("/api/admin/catalog/categories/{$categoryId}")
            ->assertOk();

        $this->assertDatabaseMissing('categories', ['id' => $categoryId]);
    }

    public function test_duplicate_category_name_returns_cat_category_name_duplicate(): void
    {
        $manager = User::factory()->manager()->create();
        Category::factory()->create(['name' => 'Snacks']);

        $this->actingAs($manager)->postJson('/api/admin/catalog/categories', [
            'name' => 'Snacks',
        ])
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'CAT_CATEGORY_NAME_DUPLICATE');
    }

    public function test_delete_category_with_products_returns_cat_category_in_use(): void
    {
        $manager = User::factory()->manager()->create();
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $this->actingAs($manager)->deleteJson("/api/admin/catalog/categories/{$category->id}")
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'CAT_CATEGORY_IN_USE');
    }
}
