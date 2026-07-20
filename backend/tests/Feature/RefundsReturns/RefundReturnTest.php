<?php

declare(strict_types=1);

namespace Tests\Feature\RefundsReturns;

use App\Domain\Sales\ValueObjects\SaleStatus;
use App\Models\CashShift;
use App\Models\PaymentLine;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Models\User;
use App\Domain\Payments\ValueObjects\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RefundReturnTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{sale_id: int, line_a: int, line_b: int, product_a: int, product_b: int, store: Store, manager: User}
     */
    private function completedSaleWithTwoLines(): array
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $manager = User::factory()->manager()->create();
        $operator->stores()->attach($store);
        $manager->stores()->attach($store);

        $shift = CashShift::factory()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
        ]);

        $productA = Product::factory()->create(['base_price' => 4000]);
        $productB = Product::factory()->create(['base_price' => 6000]);

        StoreInventory::factory()->create([
            'store_id' => $store->id,
            'product_id' => $productA->id,
            'quantity' => 4,
        ]);
        StoreInventory::factory()->create([
            'store_id' => $store->id,
            'product_id' => $productB->id,
            'quantity' => 4,
        ]);

        $sale = Sale::factory()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'cash_shift_id' => $shift->id,
            'status' => SaleStatus::Completed,
            'subtotal' => 10000,
            'discount_total' => 0,
            'total' => 10000,
            'completed_at' => now(),
        ]);

        $lineA = SaleLine::query()->create([
            'sale_id' => $sale->id,
            'product_id' => $productA->id,
            'quantity' => 1,
            'unit_price' => 4000,
            'line_discount' => 0,
            'line_total' => 4000,
        ]);
        $lineB = SaleLine::query()->create([
            'sale_id' => $sale->id,
            'product_id' => $productB->id,
            'quantity' => 1,
            'unit_price' => 6000,
            'line_discount' => 0,
            'line_total' => 6000,
        ]);

        PaymentLine::query()->create([
            'sale_id' => $sale->id,
            'method' => PaymentMethod::Pix,
            'amount' => 10000,
            'transaction_reference' => 'stub-test-ref',
        ]);

        return [
            'sale_id' => $sale->id,
            'line_a' => $lineA->id,
            'line_b' => $lineB->id,
            'product_a' => $productA->id,
            'product_b' => $productB->id,
            'store' => $store,
            'manager' => $manager,
        ];
    }

    public function test_manager_partial_refund_on_one_line(): void
    {
        $ctx = $this->completedSaleWithTwoLines();

        $response = $this->actingAs($ctx['manager'])->postJson(
            "/api/admin/sales/{$ctx['sale_id']}/refunds",
            [
                'type' => 'partial_refund',
                'reason' => 'Customer complaint on line A',
                'lines' => [
                    ['sale_line_id' => $ctx['line_a'], 'quantity' => 1],
                ],
            ],
        );

        $response
            ->assertCreated()
            ->assertJsonPath('data.refund.amount', '40.00')
            ->assertJsonPath('data.refund.type', 'partial_refund')
            ->assertJsonPath('data.refund.reason', 'Customer complaint on line A')
            ->assertJsonPath('data.refund.user_id', $ctx['manager']->id)
            ->assertJsonPath('data.refund.lines.0.restocked', false);

        $this->assertDatabaseHas('refunds', [
            'sale_id' => $ctx['sale_id'],
            'amount' => 4000,
            'user_id' => $ctx['manager']->id,
        ]);

        $this->assertDatabaseHas('store_inventories', [
            'store_id' => $ctx['store']->id,
            'product_id' => $ctx['product_a'],
            'quantity' => 4,
        ]);
    }

    public function test_full_return_restocks_and_refunds_remaining_balance(): void
    {
        $ctx = $this->completedSaleWithTwoLines();

        $this->actingAs($ctx['manager'])->postJson(
            "/api/admin/sales/{$ctx['sale_id']}/refunds",
            [
                'type' => 'full_return',
                'reason' => 'Customer returned all items',
            ],
        )
            ->assertCreated()
            ->assertJsonPath('data.refund.amount', '100.00')
            ->assertJsonPath('data.refund.lines.0.restocked', true);

        $this->assertDatabaseHas('store_inventories', [
            'store_id' => $ctx['store']->id,
            'product_id' => $ctx['product_a'],
            'quantity' => 5,
        ]);
        $this->assertDatabaseHas('store_inventories', [
            'store_id' => $ctx['store']->id,
            'product_id' => $ctx['product_b'],
            'quantity' => 5,
        ]);

        $this->actingAs($ctx['manager'])->postJson(
            "/api/admin/sales/{$ctx['sale_id']}/refunds",
            [
                'type' => 'full_refund',
                'reason' => 'Second attempt',
            ],
        )
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'REF_ALREADY_FULLY_REFUNDED');
    }

    public function test_partial_return_restocks_subset_only(): void
    {
        $ctx = $this->completedSaleWithTwoLines();

        $this->actingAs($ctx['manager'])->postJson(
            "/api/admin/sales/{$ctx['sale_id']}/refunds",
            [
                'type' => 'partial_return',
                'reason' => 'Return product A only',
                'lines' => [
                    ['sale_line_id' => $ctx['line_a'], 'quantity' => 1],
                ],
            ],
        )
            ->assertCreated()
            ->assertJsonPath('data.refund.amount', '40.00')
            ->assertJsonPath('data.refund.lines.0.restocked', true);

        $this->assertDatabaseHas('store_inventories', [
            'product_id' => $ctx['product_a'],
            'quantity' => 5,
        ]);
        $this->assertDatabaseHas('store_inventories', [
            'product_id' => $ctx['product_b'],
            'quantity' => 4,
        ]);
    }

    public function test_return_qty_exceeding_sold_returns_ref_return_qty_invalid(): void
    {
        $ctx = $this->completedSaleWithTwoLines();

        $this->actingAs($ctx['manager'])->postJson(
            "/api/admin/sales/{$ctx['sale_id']}/refunds",
            [
                'type' => 'partial_return',
                'reason' => 'Invalid qty',
                'lines' => [
                    ['sale_line_id' => $ctx['line_a'], 'quantity' => 5],
                ],
            ],
        )
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'REF_RETURN_QTY_INVALID');
    }

    public function test_refund_on_unknown_sale_returns_ref_sale_not_found(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)->postJson('/api/admin/sales/99999/refunds', [
            'type' => 'full_refund',
            'reason' => 'Missing sale',
        ])
            ->assertNotFound()
            ->assertJsonPath('error.code', 'REF_SALE_NOT_FOUND');
    }

    public function test_operator_cannot_create_refund(): void
    {
        $ctx = $this->completedSaleWithTwoLines();
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)->postJson(
            "/api/admin/sales/{$ctx['sale_id']}/refunds",
            [
                'type' => 'partial_refund',
                'reason' => 'Not allowed',
                'lines' => [
                    ['sale_line_id' => $ctx['line_a'], 'quantity' => 1],
                ],
            ],
        )
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_ADMIN_ONLY');
    }

    public function test_manager_can_list_refunds_for_sale(): void
    {
        $ctx = $this->completedSaleWithTwoLines();

        $this->actingAs($ctx['manager'])->postJson(
            "/api/admin/sales/{$ctx['sale_id']}/refunds",
            [
                'type' => 'partial_refund',
                'reason' => 'Audit list',
                'lines' => [
                    ['sale_line_id' => $ctx['line_a'], 'quantity' => 1],
                ],
            ],
        )->assertCreated();

        $this->actingAs($ctx['manager'])
            ->getJson("/api/admin/sales/{$ctx['sale_id']}/refunds")
            ->assertOk()
            ->assertJsonCount(1, 'data.refunds')
            ->assertJsonPath('data.refunds.0.reason', 'Audit list');
    }
}
