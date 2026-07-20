<?php

declare(strict_types=1);

namespace Tests\Feature\Sales;

use App\Domain\CashShift\ValueObjects\CashShiftStatus;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class HoldSaleTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    public function test_operator_can_park_sale_with_label_and_list_held_sales(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create(['base_price' => 1500]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $holdResponse = $this->postJson("/api/operational/sales/{$saleId}/hold", [
            'label' => 'Table 12',
        ]);

        $holdResponse
            ->assertOk()
            ->assertJsonPath('data.message', 'Sale parked on hold.')
            ->assertJsonPath('data.sale.status', 'held')
            ->assertJsonPath('data.sale.hold_label', 'Table 12')
            ->assertJsonPath('data.sale.total', '30.00');

        $this->assertDatabaseHas('sales', [
            'id' => $saleId,
            'status' => 'held',
            'hold_label' => 'Table 12',
        ]);

        $this->getJson('/api/operational/sales/held')
            ->assertOk()
            ->assertJsonCount(1, 'data.sales')
            ->assertJsonPath('data.sales.0.id', $saleId)
            ->assertJsonPath('data.sales.0.hold_label', 'Table 12');
    }

    public function test_operator_can_resume_held_sale_and_continue_editing(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create(['base_price' => 1000]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $addResponse = $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $lineId = (int) $addResponse->json('data.sale.lines.0.id');

        $this->postJson("/api/operational/sales/{$saleId}/hold")->assertOk();

        $this->postJson("/api/operational/sales/{$saleId}/resume")
            ->assertOk()
            ->assertJsonPath('data.sale.status', 'in_progress')
            ->assertJsonPath('data.sale.hold_label', null);

        $this->patchJson("/api/operational/sales/{$saleId}/lines/{$lineId}", [
            'quantity' => 3,
        ])->assertOk();
    }

    public function test_parking_empty_cart_returns_sale_empty_cart(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/hold")
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'SALE_EMPTY_CART');
    }

    public function test_resuming_non_held_sale_returns_sale_not_held(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/resume")
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'SALE_NOT_HELD');
    }

    public function test_modifying_held_sale_returns_sale_cart_held(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $shift = $this->withOpenShift($operator, $store);
        $product = Product::factory()->create();

        $sale = Sale::factory()->held('Counter A')->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'cash_shift_id' => $shift->id,
            'total' => 1000,
        ]);

        $sale->lines()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 1000,
            'line_discount' => 0,
            'line_total' => 1000,
        ]);

        $lineId = $sale->lines()->first()->id;

        $this->actingAsOperatorAtStore($operator, $store)
            ->patchJson("/api/operational/sales/{$sale->id}/lines/{$lineId}", [
                'quantity' => 2,
            ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'SALE_CART_HELD');
    }

    public function test_held_sales_from_other_shift_are_not_listed(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $shiftA = $this->withOpenShift($operator, $store);

        Sale::factory()->held('Old shift')->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'cash_shift_id' => $shiftA->id,
        ]);

        $shiftA->update([
            'status' => CashShiftStatus::Closed,
            'closed_at' => now(),
        ]);

        $shiftB = $this->withOpenShift($operator, $store);

        $this->actingAsOperatorAtStore($operator, $store)
            ->getJson('/api/operational/sales/held')
            ->assertOk()
            ->assertJsonCount(0, 'data.sales');

        Sale::factory()->held('Current shift')->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'cash_shift_id' => $shiftB->id,
        ]);

        $this->getJson('/api/operational/sales/held')
            ->assertOk()
            ->assertJsonCount(1, 'data.sales');
    }
}
