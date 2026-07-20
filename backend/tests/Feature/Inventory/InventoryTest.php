<?php

declare(strict_types=1);

namespace Tests\Feature\Inventory;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class InventorySaleTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    public function test_completing_sale_decrements_tracked_stock(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create(['base_price' => 1000]);
        StoreInventory::factory()->create([
            'store_id' => $store->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'track_stock' => true,
        ]);

        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'pix', 'amount' => 30.00],
            ],
        ])->assertOk();

        $this->assertDatabaseHas('store_inventories', [
            'store_id' => $store->id,
            'product_id' => $product->id,
            'quantity' => 7,
        ]);
    }

    public function test_adding_line_to_cart_does_not_decrement_stock(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create(['base_price' => 500]);
        StoreInventory::factory()->create([
            'store_id' => $store->id,
            'product_id' => $product->id,
            'quantity' => 4,
        ]);

        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertOk();

        $this->assertDatabaseHas('store_inventories', [
            'store_id' => $store->id,
            'product_id' => $product->id,
            'quantity' => 4,
        ]);
    }

    public function test_completing_sale_with_insufficient_stock_returns_inv_insufficient_stock(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create(['base_price' => 1000]);
        StoreInventory::factory()->create([
            'store_id' => $store->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'pix', 'amount' => 30.00],
            ],
        ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'INV_INSUFFICIENT_STOCK');

        $this->assertDatabaseHas('store_inventories', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->assertDatabaseHas('sales', [
            'id' => $saleId,
            'status' => 'in_progress',
        ]);
    }

    public function test_untracked_product_completes_without_inventory_row(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create(['base_price' => 800]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'cash', 'amount' => 8.00, 'cash_received' => 10.00],
            ],
        ])->assertOk();
    }
}

final class AdminInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_adjust_stock_with_reason(): void
    {
        $manager = User::factory()->manager()->create();
        $store = Store::factory()->create();
        $manager->stores()->attach($store);
        $product = Product::factory()->create();

        $response = $this->actingAs($manager)->postJson('/api/admin/inventory/adjustments', [
            'store_id' => $store->id,
            'product_id' => $product->id,
            'quantity' => 25,
            'reason' => 'Initial stock count',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.inventory.quantity', 25);

        $this->assertDatabaseHas('store_inventories', [
            'store_id' => $store->id,
            'product_id' => $product->id,
            'quantity' => 25,
        ]);

        $this->assertDatabaseHas('stock_adjustments', [
            'store_id' => $store->id,
            'product_id' => $product->id,
            'user_id' => $manager->id,
            'reason' => 'Initial stock count',
        ]);
    }

    public function test_adjustment_without_reason_returns_validation_error(): void
    {
        $manager = User::factory()->manager()->create();
        $store = Store::factory()->create();
        $manager->stores()->attach($store);
        $product = Product::factory()->create();

        $this->actingAs($manager)->postJson('/api/admin/inventory/adjustments', [
            'store_id' => $store->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'reason' => '',
        ])->assertStatus(422);
    }

    public function test_manager_can_list_store_inventory(): void
    {
        $manager = User::factory()->manager()->create();
        $store = Store::factory()->create();
        $manager->stores()->attach($store);
        StoreInventory::factory()->count(2)->create(['store_id' => $store->id]);

        $this->actingAs($manager)
            ->getJson('/api/admin/inventory?store_id='.$store->id)
            ->assertOk()
            ->assertJsonCount(2, 'data.inventory');
    }

    public function test_operator_cannot_adjust_stock(): void
    {
        $operator = User::factory()->operator()->create();
        $store = Store::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($operator)->postJson('/api/admin/inventory/adjustments', [
            'store_id' => $store->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'reason' => 'Should fail',
        ])
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_ADMIN_ONLY');
    }
}
