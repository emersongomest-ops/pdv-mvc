<?php

declare(strict_types=1);

namespace Tests\Feature\Sales;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Models\User;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class CompleteSaleTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    public function test_operator_can_complete_sale_with_cash_payment_and_fiscal_receipt(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $shift = $this->withOpenShift($operator, $store);
        $product = Product::factory()->create(['base_price' => 1250]);

        $this->actingAsOperatorAtStore($operator, $store);

        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertOk();

        $response = $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                [
                    'method' => 'cash',
                    'amount' => 25.00,
                    'cash_received' => 30.00,
                ],
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.message', 'Sale completed.')
            ->assertJsonPath('data.sale.status', 'completed')
            ->assertJsonPath('data.sale.store_id', $store->id)
            ->assertJsonPath('data.sale.operator_id', $operator->id)
            ->assertJsonPath('data.sale.cash_shift_id', $shift->id)
            ->assertJsonPath('data.sale.total', '25.00')
            ->assertJsonPath('data.sale.payments.0.method', 'cash')
            ->assertJsonPath('data.sale.payments.0.change_amount', '5.00')
            ->assertJsonPath('data.sale.fiscal_receipt.receipt_number', sprintf('FR-%08d', $saleId));

        $this->assertDatabaseHas('sales', [
            'id' => $saleId,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('payment_lines', [
            'sale_id' => $saleId,
            'method' => 'cash',
            'amount' => 2500,
            'change_amount' => 500,
        ]);

        $this->assertDatabaseHas('fiscal_receipts', [
            'sale_id' => $saleId,
            'receipt_number' => sprintf('FR-%08d', $saleId),
        ]);
    }

    public function test_operator_can_complete_sale_with_split_payments(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create(['base_price' => 1000]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'pix', 'amount' => 20.00],
                ['method' => 'cash', 'amount' => 10.00, 'cash_received' => 10.00],
            ],
        ])
            ->assertOk()
            ->assertJsonPath('data.sale.total', '30.00')
            ->assertJsonCount(2, 'data.sale.payments');
    }

    public function test_completing_sale_uses_constant_inventory_queries_for_multiple_lines(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $products = Product::factory()->count(3)->create(['base_price' => 1000]);
        foreach ($products as $product) {
            StoreInventory::factory()->create([
                'store_id' => $store->id,
                'product_id' => $product->id,
                'quantity' => 5,
                'track_stock' => true,
            ]);
        }

        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');
        foreach ($products as $product) {
            $this->postJson("/api/operational/sales/{$saleId}/lines", [
                'product_id' => $product->id,
                'quantity' => 1,
            ])->assertOk();
        }

        $inventoryQueries = [];
        DB::listen(static function (QueryExecuted $query) use (&$inventoryQueries): void {
            if (str_contains($query->sql, 'store_inventories')) {
                $inventoryQueries[] = $query->sql;
            }
        });

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'pix', 'amount' => 30.00],
            ],
        ])->assertOk();

        $this->assertLessThanOrEqual(
            2,
            count($inventoryQueries),
            'Inventory query count must not grow with the number of sale lines.',
        );

        foreach ($products as $product) {
            $this->assertDatabaseHas('store_inventories', [
                'store_id' => $store->id,
                'product_id' => $product->id,
                'quantity' => 4,
            ]);
        }
    }

    public function test_completing_empty_cart_returns_sale_empty_cart(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'cash', 'amount' => 10.00, 'cash_received' => 10.00],
            ],
        ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'SALE_EMPTY_CART');
    }

    public function test_completing_without_payments_returns_validation_error(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create();
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [],
        ])->assertStatus(422);
    }

    public function test_payment_mismatch_returns_sale_payment_mismatch(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create(['base_price' => 1000]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'pix', 'amount' => 9.00],
            ],
        ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'SALE_PAYMENT_MISMATCH');
    }

    public function test_insufficient_cash_returns_pay_cash_insufficient(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create(['base_price' => 1000]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'cash', 'amount' => 10.00, 'cash_received' => 5.00],
            ],
        ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'PAY_CASH_INSUFFICIENT');
    }

    public function test_completing_already_completed_sale_returns_sale_already_completed(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $shift = $this->withOpenShift($operator, $store);

        $sale = Sale::factory()->completed()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'cash_shift_id' => $shift->id,
            'total' => 1000,
        ]);

        $this->actingAsOperatorAtStore($operator, $store)
            ->postJson("/api/operational/sales/{$sale->id}/complete", [
                'payments' => [
                    ['method' => 'pix', 'amount' => 10.00],
                ],
            ])
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'SALE_ALREADY_COMPLETED');
    }

    public function test_unsupported_payment_method_returns_validation_error(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create(['base_price' => 1000]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'bitcoin', 'amount' => 10.00],
            ],
        ])->assertStatus(422);
    }
}
