<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\CashShift;
use App\Models\Customer;
use App\Models\CustomerStoreStat;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\ActsWithAdminStoreAccess;
use Tests\TestCase;

final class AdminDashboardMetricsTest extends TestCase
{
    use ActsWithAdminStoreAccess;
    use RefreshDatabase;

    #[Test]
    public function manager_dashboard_returns_parallel_kpi_metrics(): void
    {
        $store = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $this->attachManagerToStore($manager, $store);
        $operator = User::factory()->operator()->create();

        Product::factory()->count(2)->create(['is_active' => true]);
        Product::factory()->create(['is_active' => false]);
        $customers = Customer::factory()->count(2)->create();
        foreach ($customers as $customer) {
            CustomerStoreStat::query()->create([
                'customer_id' => $customer->id,
                'store_id' => $store->id,
                'purchase_count' => 1,
                'total_spend' => 500,
            ]);
        }

        $shift = CashShift::factory()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
        ]);

        Sale::factory()->completed()->count(3)->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'cash_shift_id' => $shift->id,
        ]);

        $response = $this->actingAs($manager)->getJson('/api/admin/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath('data.area', 'admin')
            ->assertJsonPath('data.user_id', $manager->id)
            ->assertJsonPath('data.metrics.products_total', 3)
            ->assertJsonPath('data.metrics.products_active', 2)
            ->assertJsonPath('data.metrics.products_inactive', 1)
            ->assertJsonPath('data.metrics.customers_total', 2)
            ->assertJsonPath('data.metrics.sales_completed', 3)
            ->assertJsonPath('data.metrics.open_shifts', 1);
    }
}
