<?php

declare(strict_types=1);

namespace Tests\Feature\Promotions;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class ApplyPromotionToSaleTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    public function test_recurring_customer_without_promotion_pays_full_price(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $customer = Customer::factory()->create(['lifetime_spend' => 50000]);
        $product = Product::factory()->create(['base_price' => 10000]);

        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');
        $this->postJson("/api/operational/sales/{$saleId}/customer", ['customer_id' => $customer->id]);
        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->getJson("/api/operational/sales/{$saleId}")
            ->assertOk()
            ->assertJsonPath('data.sale.total', '100.00')
            ->assertJsonPath('data.sale.discount_total', '0.00')
            ->assertJsonCount(0, 'data.sale.promotions');
    }

    public function test_operator_applies_assigned_promotion_and_total_reflects_discount(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $customer = Customer::factory()->create();
        $promotion = Promotion::factory()->forAssignedCustomers()->create([
            'code' => 'SUMMER10',
            'discount_type' => 'percent',
            'discount_value' => 1000,
        ]);
        $promotion->customers()->attach($customer->id);

        $product = Product::factory()->create(['base_price' => 10000]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/customer", ['customer_id' => $customer->id]);
        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->postJson("/api/operational/sales/{$saleId}/promotions", [
            'code' => 'SUMMER10',
        ])
            ->assertOk()
            ->assertJsonPath('data.sale.discount_total', '10.00')
            ->assertJsonPath('data.sale.total', '90.00')
            ->assertJsonPath('data.sale.promotions.0.code', 'SUMMER10');
    }

    public function test_unique_promotion_blocks_second_promotion(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $unique = Promotion::factory()->uniqueStacking()->create([
            'code' => 'UNIQUE15',
            'discount_value' => 1500,
        ]);
        $other = Promotion::factory()->create(['code' => 'EXTRA5', 'discount_value' => 500]);

        $product = Product::factory()->create(['base_price' => 10000]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');
        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->postJson("/api/operational/sales/{$saleId}/promotions", ['code' => $unique->code])
            ->assertOk();

        $this->postJson("/api/operational/sales/{$saleId}/promotions", ['code' => $other->code])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'PROMO_NOT_COMBINABLE');
    }

    public function test_accumulable_promotions_stack_together(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        Promotion::factory()->create(['code' => 'ACC10', 'discount_value' => 1000]);
        Promotion::factory()->create(['code' => 'ACC5', 'discount_value' => 500]);

        $product = Product::factory()->create(['base_price' => 10000]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');
        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->postJson("/api/operational/sales/{$saleId}/promotions", ['code' => 'ACC10'])->assertOk();
        $this->postJson("/api/operational/sales/{$saleId}/promotions", ['code' => 'ACC5'])
            ->assertOk()
            ->assertJsonPath('data.sale.discount_total', '15.00')
            ->assertJsonPath('data.sale.total', '85.00')
            ->assertJsonCount(2, 'data.sale.promotions');
    }

    public function test_unassigned_customer_promotion_returns_promo_not_assigned(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $customer = Customer::factory()->create();
        $other = Customer::factory()->create();
        $promotion = Promotion::factory()->forAssignedCustomers()->create(['code' => 'VIP20']);
        $promotion->customers()->attach($other->id);

        $product = Product::factory()->create(['base_price' => 5000]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');
        $this->postJson("/api/operational/sales/{$saleId}/customer", ['customer_id' => $customer->id]);
        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->postJson("/api/operational/sales/{$saleId}/promotions", ['code' => 'VIP20'])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'PROMO_NOT_ASSIGNED');
    }

    public function test_expired_promotion_returns_promo_expired(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        Promotion::factory()->expired()->create(['code' => 'OLD10']);

        $product = Product::factory()->create(['base_price' => 5000]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');
        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->postJson("/api/operational/sales/{$saleId}/promotions", ['code' => 'OLD10'])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'PROMO_EXPIRED');
    }

    public function test_fixed_discount_never_makes_total_negative(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        Promotion::factory()->fixed('200.00')->create(['code' => 'BIGFIX']);

        $product = Product::factory()->create(['base_price' => 5000]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');
        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->postJson("/api/operational/sales/{$saleId}/promotions", ['code' => 'BIGFIX'])
            ->assertOk()
            ->assertJsonPath('data.sale.total', '0.00')
            ->assertJsonPath('data.sale.discount_total', '50.00');
    }

    public function test_unknown_promotion_code_returns_promo_not_found(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/promotions", ['code' => 'NOPE'])
            ->assertNotFound()
            ->assertJsonPath('error.code', 'PROMO_NOT_FOUND');
    }
}
