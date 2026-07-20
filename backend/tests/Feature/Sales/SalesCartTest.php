<?php

declare(strict_types=1);

namespace Tests\Feature\Sales;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class SalesCartTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    public function test_operator_can_create_sale_with_first_line_in_one_request(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);
        $product = Product::factory()->create(['base_price' => 1500]);

        $response = $this->postJson('/api/operational/sales', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.message', 'Sale created with line.')
            ->assertJsonPath('data.sale.status', 'in_progress')
            ->assertJsonPath('data.sale.total', '30.00')
            ->assertJsonCount(1, 'data.sale.lines')
            ->assertJsonPath('data.sale.lines.0.quantity', 2);
    }

    public function test_create_sale_with_product_requires_quantity(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);
        $product = Product::factory()->create();

        $this->postJson('/api/operational/sales', [
            'product_id' => $product->id,
        ])->assertStatus(422);
    }

    public function test_operator_can_create_in_progress_sale(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $shift = $this->withOpenShift($operator, $store);

        $response = $this
            ->actingAsOperatorAtStore($operator, $store)
            ->postJson('/api/operational/sales');

        $response
            ->assertCreated()
            ->assertJsonPath('data.sale.status', 'in_progress')
            ->assertJsonPath('data.sale.store_id', $store->id)
            ->assertJsonPath('data.sale.total', '0.00');

        $this->assertDatabaseHas('sales', [
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'cash_shift_id' => $shift->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_operator_can_add_update_and_remove_cart_lines(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $productA = Product::factory()->create(['base_price' => 1000]);
        $productB = Product::factory()->create(['base_price' => 2550]);

        $createResponse = $this->postJson('/api/operational/sales');
        $saleId = (int) $createResponse->json('data.sale.id');

        $addResponse = $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $productA->id,
            'quantity' => 2,
        ]);

        $addResponse
            ->assertOk()
            ->assertJsonPath('data.sale.total', '20.00')
            ->assertJsonCount(1, 'data.sale.lines');

        $lineId = (int) $addResponse->json('data.sale.lines.0.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $productB->id,
            'quantity' => 1,
        ])
            ->assertOk()
            ->assertJsonPath('data.sale.total', '45.50')
            ->assertJsonCount(2, 'data.sale.lines');

        $this->patchJson("/api/operational/sales/{$saleId}/lines/{$lineId}", [
            'quantity' => 3,
        ])
            ->assertOk()
            ->assertJsonPath('data.sale.total', '55.50');

        $this->deleteJson("/api/operational/sales/{$saleId}/lines/{$lineId}")
            ->assertOk()
            ->assertJsonPath('data.sale.total', '25.50')
            ->assertJsonCount(1, 'data.sale.lines');
    }

    public function test_adding_same_product_merges_quantity(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create(['base_price' => 500]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertOk();

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 3,
        ])
            ->assertOk()
            ->assertJsonPath('data.sale.lines.0.quantity', 5)
            ->assertJsonPath('data.sale.total', '25.00')
            ->assertJsonCount(1, 'data.sale.lines');
    }

    public function test_adding_inactive_product_returns_inv_product_inactive(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->inactive()->create();
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'INV_PRODUCT_INACTIVE');
    }

    public function test_modifying_completed_sale_returns_sale_already_completed(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $shift = $this->withOpenShift($operator, $store);
        $product = Product::factory()->create();

        $sale = Sale::factory()->completed()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'cash_shift_id' => $shift->id,
        ]);

        $line = SaleLine::query()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 1000,
            'line_discount' => 0,
            'line_total' => 1000,
        ]);

        $this->actingAsOperatorAtStore($operator, $store)
            ->patchJson("/api/operational/sales/{$sale->id}/lines/{$line->id}", [
                'quantity' => 2,
            ])
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'SALE_ALREADY_COMPLETED');
    }

    public function test_accessing_another_operators_sale_returns_sale_not_found(): void
    {
        $store = Store::factory()->create();
        $operatorA = User::factory()->operator()->create();
        $operatorB = User::factory()->operator()->create();
        $shiftA = $this->withOpenShift($operatorA, $store);

        $sale = Sale::factory()->create([
            'store_id' => $store->id,
            'user_id' => $operatorA->id,
            'cash_shift_id' => $shiftA->id,
        ]);

        $this->withOpenShift($operatorB, $store);

        $this->actingAsOperatorAtStore($operatorB, $store)
            ->getJson("/api/operational/sales/{$sale->id}")
            ->assertNotFound()
            ->assertJsonPath('error.code', 'SALE_NOT_FOUND');
    }

    public function test_sales_routes_require_open_shift(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();

        $this->actingAsOperatorAtStore($operator, $store)
            ->postJson('/api/operational/sales')
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'SHIFT_NOT_OPEN');
    }
}
