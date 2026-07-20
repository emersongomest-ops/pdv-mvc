<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Domain\Sales\ValueObjects\SaleStatus;
use App\Models\CashShift;
use App\Models\Customer;
use App\Models\CustomerStoreStat;
use App\Models\PaymentLine;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Models\User;
use App\Notifications\Sales\SaleCompletedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\ActsWithAdminStoreAccess;
use Tests\Support\ActsWithOperationalSession;
use Tests\TestCase;

final class AdminStoreAccessIdorTest extends TestCase
{
    use ActsWithAdminStoreAccess;
    use ActsWithOperationalSession;
    use RefreshDatabase;

    #[Test]
    public function dashboard_counts_only_assigned_store_kpis(): void
    {
        $storeA = Store::factory()->create();
        $storeB = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $this->attachManagerToStore($manager, $storeA);
        $operator = User::factory()->operator()->create();

        Product::factory()->count(2)->create(['is_active' => true]);
        $customerA = Customer::factory()->create();
        Customer::factory()->create();
        CustomerStoreStat::query()->create([
            'customer_id' => $customerA->id,
            'store_id' => $storeA->id,
            'purchase_count' => 1,
            'total_spend' => 1000,
        ]);

        $shiftA = CashShift::factory()->create([
            'store_id' => $storeA->id,
            'user_id' => $operator->id,
        ]);
        CashShift::factory()->create([
            'store_id' => $storeB->id,
            'user_id' => $operator->id,
        ]);

        Sale::factory()->completed()->count(2)->create([
            'store_id' => $storeA->id,
            'user_id' => $operator->id,
            'cash_shift_id' => $shiftA->id,
        ]);
        Sale::factory()->completed()->count(3)->create([
            'store_id' => $storeB->id,
            'user_id' => $operator->id,
        ]);

        $this->actingAs($manager)
            ->getJson('/api/admin/dashboard')
            ->assertOk()
            ->assertJsonPath('data.metrics.products_total', 2)
            ->assertJsonPath('data.metrics.customers_total', 1)
            ->assertJsonPath('data.metrics.sales_completed', 2)
            ->assertJsonPath('data.metrics.open_shifts', 1);
    }

    #[Test]
    public function manager_without_stores_sees_empty_store_scoped_kpis(): void
    {
        $manager = User::factory()->manager()->create();
        Product::factory()->create();
        Sale::factory()->completed()->create();
        CashShift::factory()->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/dashboard')
            ->assertOk()
            ->assertJsonPath('data.metrics.products_total', 1)
            ->assertJsonPath('data.metrics.customers_total', 0)
            ->assertJsonPath('data.metrics.sales_completed', 0)
            ->assertJsonPath('data.metrics.open_shifts', 0);
    }

    #[Test]
    public function sales_list_defaults_to_assigned_stores_only(): void
    {
        $storeA = Store::factory()->create();
        $storeB = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $this->attachManagerToStore($manager, $storeA);
        $operator = User::factory()->operator()->create();

        $saleA = Sale::factory()->completed()->create([
            'store_id' => $storeA->id,
            'user_id' => $operator->id,
        ]);
        Sale::factory()->completed()->create([
            'store_id' => $storeB->id,
            'user_id' => $operator->id,
        ]);

        $this->actingAs($manager)
            ->getJson('/api/admin/sales')
            ->assertOk()
            ->assertJsonCount(1, 'data.sales')
            ->assertJsonPath('data.sales.0.id', $saleA->id);
    }

    #[Test]
    public function sales_filter_and_show_deny_unassigned_store(): void
    {
        $storeA = Store::factory()->create();
        $storeB = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $this->attachManagerToStore($manager, $storeA);
        $operator = User::factory()->operator()->create();

        $saleB = Sale::factory()->completed()->create([
            'store_id' => $storeB->id,
            'user_id' => $operator->id,
        ]);

        $this->actingAs($manager)
            ->getJson('/api/admin/sales?store_id='.$storeB->id)
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_STORE_ACCESS_DENIED');

        $this->actingAs($manager)
            ->getJson("/api/admin/sales/{$saleB->id}")
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_STORE_ACCESS_DENIED');
    }

    #[Test]
    public function shifts_and_inventory_deny_unassigned_store(): void
    {
        $storeA = Store::factory()->create();
        $storeB = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $this->attachManagerToStore($manager, $storeA);
        $operator = User::factory()->operator()->create();
        $shiftB = $this->withOpenShift($operator, $storeB);
        $product = Product::factory()->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/shifts?store_id='.$storeB->id)
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_STORE_ACCESS_DENIED');

        $this->actingAs($manager)
            ->getJson("/api/admin/shifts/{$shiftB->id}/report")
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_STORE_ACCESS_DENIED');

        $this->actingAs($manager)
            ->getJson('/api/admin/inventory?store_id='.$storeB->id)
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_STORE_ACCESS_DENIED');

        $this->actingAs($manager)
            ->postJson('/api/admin/inventory/adjustments', [
                'store_id' => $storeB->id,
                'product_id' => $product->id,
                'quantity' => 5,
                'reason' => 'Should fail',
            ])
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_STORE_ACCESS_DENIED');

        $this->assertDatabaseMissing('stock_adjustments', [
            'store_id' => $storeB->id,
            'product_id' => $product->id,
        ]);
    }

    #[Test]
    public function refunds_deny_sale_from_unassigned_store(): void
    {
        $storeA = Store::factory()->create();
        $storeB = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $this->attachManagerToStore($manager, $storeA);
        $operator = User::factory()->operator()->create();

        $saleB = Sale::factory()->completed()->create([
            'store_id' => $storeB->id,
            'user_id' => $operator->id,
            'status' => SaleStatus::Completed,
            'subtotal' => 1000,
            'total' => 1000,
        ]);
        $product = Product::factory()->create(['base_price' => 1000]);
        SaleLine::query()->create([
            'sale_id' => $saleB->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 1000,
            'line_discount' => 0,
            'line_total' => 1000,
        ]);
        PaymentLine::query()->create([
            'sale_id' => $saleB->id,
            'method' => PaymentMethod::Pix,
            'amount' => 1000,
            'transaction_reference' => 'ref-b',
        ]);
        StoreInventory::factory()->create([
            'store_id' => $storeB->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $this->actingAs($manager)
            ->getJson("/api/admin/sales/{$saleB->id}/refunds")
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_STORE_ACCESS_DENIED');

        $this->actingAs($manager)
            ->postJson("/api/admin/sales/{$saleB->id}/refunds", [
                'type' => 'full_return',
                'reason' => 'Should fail',
            ])
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_STORE_ACCESS_DENIED');

        $this->assertDatabaseMissing('refunds', ['sale_id' => $saleB->id]);
    }

    #[Test]
    public function notifications_hide_entries_for_revoked_store_assignment(): void
    {
        $storeA = Store::factory()->create();
        $storeB = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $this->attachManagerToStore($manager, $storeA);
        $manager->stores()->attach($storeB);

        $manager->notify(new SaleCompletedNotification(
            saleId: 10,
            storeId: $storeA->id,
            operatorId: 1,
            totalCents: 1000,
        ));
        $manager->notify(new SaleCompletedNotification(
            saleId: 20,
            storeId: $storeB->id,
            operatorId: 1,
            totalCents: 2000,
        ));

        $manager->stores()->detach($storeB);

        $this->actingAs($manager)
            ->getJson('/api/admin/notifications')
            ->assertOk()
            ->assertJsonCount(1, 'data.notifications')
            ->assertJsonPath('data.notifications.0.data.sale_id', 10);
    }

    #[Test]
    public function catalog_remains_global_without_store_assignment(): void
    {
        $manager = User::factory()->manager()->create();
        Product::factory()->create(['name' => 'Global Product']);

        $this->actingAs($manager)
            ->getJson('/api/admin/catalog/products')
            ->assertOk()
            ->assertJsonCount(1, 'data.products');
    }
}
