<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminCatalogAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_cannot_access_admin_catalog_routes(): void
    {
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)->getJson('/api/admin/catalog/products')
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_ADMIN_ONLY');

        $this->actingAs($operator)->postJson('/api/admin/catalog/products', [
            'sku' => 'SKU-001',
            'name' => 'Blocked Product',
            'base_price' => 10.00,
        ])
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_ADMIN_ONLY');
    }

    public function test_manager_can_access_admin_catalog_routes(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)->getJson('/api/admin/catalog/categories')->assertOk();
        $this->actingAs($manager)->getJson('/api/admin/catalog/products')->assertOk();
    }
}
