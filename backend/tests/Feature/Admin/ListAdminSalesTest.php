<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Models\Customer;
use App\Models\PaymentLine;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\ActsWithAdminStoreAccess;
use Tests\TestCase;

final class ListAdminSalesTest extends TestCase
{
    use ActsWithAdminStoreAccess;
    use RefreshDatabase;

    #[Test]
    public function manager_can_list_completed_sales_with_filters(): void
    {
        $storeA = Store::factory()->create(['code' => 'A1']);
        $storeB = Store::factory()->create(['code' => 'B1']);
        $manager = User::factory()->manager()->create();
        $this->attachManagerToStore($manager, $storeA);
        $this->attachManagerToStore($manager, $storeB);
        $operatorA = User::factory()->operator()->create(['name' => 'Op A']);
        $operatorB = User::factory()->operator()->create(['name' => 'Op B']);
        $customer = Customer::factory()->create(['name' => 'Maria']);

        $saleMatch = Sale::factory()->completed()->create([
            'store_id' => $storeA->id,
            'user_id' => $operatorA->id,
            'customer_id' => $customer->id,
            'subtotal' => 1000,
            'discount_total' => 0,
            'total' => 1000,
            'completed_at' => now()->subDay(),
        ]);
        PaymentLine::query()->create([
            'sale_id' => $saleMatch->id,
            'method' => PaymentMethod::Pix,
            'amount' => 1000,
        ]);

        $saleOtherStore = Sale::factory()->completed()->create([
            'store_id' => $storeB->id,
            'user_id' => $operatorA->id,
            'subtotal' => 2000,
            'total' => 2000,
            'completed_at' => now()->subDay(),
        ]);
        PaymentLine::query()->create([
            'sale_id' => $saleOtherStore->id,
            'method' => PaymentMethod::Pix,
            'amount' => 2000,
        ]);

        $saleCash = Sale::factory()->completed()->create([
            'store_id' => $storeA->id,
            'user_id' => $operatorB->id,
            'subtotal' => 500,
            'total' => 500,
            'completed_at' => now()->subDay(),
        ]);
        PaymentLine::query()->create([
            'sale_id' => $saleCash->id,
            'method' => PaymentMethod::Cash,
            'amount' => 500,
        ]);

        Sale::factory()->create([
            'store_id' => $storeA->id,
            'user_id' => $operatorA->id,
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($manager)->getJson('/api/admin/sales?'.http_build_query([
            'store_id' => $storeA->id,
            'operator_id' => $operatorA->id,
            'customer_id' => $customer->id,
            'payment_method' => 'pix',
            'from' => now()->subDays(2)->toDateString(),
            'to' => now()->toDateString(),
        ]));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data.sales')
            ->assertJsonPath('data.sales.0.id', $saleMatch->id)
            ->assertJsonPath('data.sales.0.store_code', 'A1')
            ->assertJsonPath('data.sales.0.operator_name', 'Op A')
            ->assertJsonPath('data.sales.0.customer_name', 'Maria')
            ->assertJsonPath('data.sales.0.total', '10.00')
            ->assertJsonPath('data.sales.0.payment_methods.0', 'pix');
    }

    #[Test]
    public function manager_lists_only_completed_sales_by_default(): void
    {
        $store = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $this->attachManagerToStore($manager, $store);
        $operator = User::factory()->operator()->create();

        $completed = Sale::factory()->completed()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'total' => 1500,
            'subtotal' => 1500,
        ]);
        Sale::factory()->held()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
        ]);

        $this->actingAs($manager)
            ->getJson('/api/admin/sales')
            ->assertOk()
            ->assertJsonCount(1, 'data.sales')
            ->assertJsonPath('data.sales.0.id', $completed->id);
    }

    #[Test]
    public function validation_rejects_invalid_payment_method_and_date_range(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/sales?payment_method=bitcoin')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['payment_method']);

        $this->actingAs($manager)
            ->getJson('/api/admin/sales?from=2026-07-10&to=2026-07-01')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['to']);
    }

    #[Test]
    public function operator_cannot_list_admin_sales(): void
    {
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)
            ->getJson('/api/admin/sales')
            ->assertForbidden();
    }
}
