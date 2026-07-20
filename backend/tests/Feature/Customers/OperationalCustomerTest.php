<?php

declare(strict_types=1);

namespace Tests\Feature\Customers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class OperationalCustomerTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    public function test_operator_can_find_customer_by_cpf(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorAtStore($operator, $store);

        Customer::factory()->create([
            'name' => 'Maria Silva',
            'cpf' => '52998224725',
        ]);

        $this->getJson('/api/operational/customers?cpf=529.982.247-25')
            ->assertOk()
            ->assertJsonPath('data.customer.name', 'Maria Silva')
            ->assertJsonPath('data.customer.cpf', '529.***.***-25');
    }

    public function test_unknown_cpf_returns_cust_not_found(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorAtStore($operator, $store);

        $this->getJson('/api/operational/customers?cpf=11144477735')
            ->assertNotFound()
            ->assertJsonPath('error.code', 'CUST_NOT_FOUND');
    }

    public function test_operator_can_register_customer_at_pos(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorAtStore($operator, $store);

        $this->postJson('/api/operational/customers', [
            'name' => 'João Pedro',
            'email' => 'joao@example.com',
            'cpf' => '390.533.447-05',
            'phone' => '11977776666',
            'birth_date' => '1988-03-20',
            'address' => 'Rua A, 50 - Campinas - SP',
        ])
            ->assertCreated()
            ->assertJsonPath('data.customer.cpf', '39053344705');
    }

    public function test_operator_can_attach_customer_to_sale(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $customer = Customer::factory()->create();
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/customer", [
            'customer_id' => $customer->id,
        ])
            ->assertOk()
            ->assertJsonPath('data.sale.customer_id', $customer->id);

        $this->assertDatabaseHas('sales', [
            'id' => $saleId,
            'customer_id' => $customer->id,
        ]);
    }

    public function test_attach_unknown_customer_returns_cust_not_found(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/customer", [
            'customer_id' => 99999,
        ])
            ->assertNotFound()
            ->assertJsonPath('error.code', 'CUST_NOT_FOUND');
    }

    public function test_completing_sale_with_customer_updates_spend_stats(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $customer = Customer::factory()->create(['lifetime_spend' => 0]);
        $product = Product::factory()->create(['base_price' => 1500]);

        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/customer", [
            'customer_id' => $customer->id,
        ])->assertOk();

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertOk();

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'pix', 'amount' => 30.00],
            ],
        ])->assertOk();

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'lifetime_spend' => 3000,
        ]);

        $this->assertDatabaseHas('customer_store_stats', [
            'customer_id' => $customer->id,
            'store_id' => $store->id,
            'purchase_count' => 1,
            'total_spend' => 3000,
        ]);
    }

    public function test_completing_sale_without_customer_still_works(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create(['base_price' => 1000]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'pix', 'amount' => 10.00],
            ],
        ])
            ->assertOk()
            ->assertJsonPath('data.sale.customer_id', null);
    }
}
