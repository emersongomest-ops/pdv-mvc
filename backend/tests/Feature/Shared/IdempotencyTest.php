<?php

declare(strict_types=1);

namespace Tests\Feature\Shared;

use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Domain\Sales\ValueObjects\SaleStatus;
use App\Models\CashShift;
use App\Models\IdempotencyRecord;
use App\Models\PaymentLine;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class IdempotencyTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    public function test_complete_sale_without_idempotency_key_returns_422(): void
    {
        [$operator, $store, $saleId] = $this->openCartReadyToPay();

        $this->withoutAutoIdempotencyKey();
        $this->actingAsOperatorAtStore($operator, $store);

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'pix', 'amount' => 10.00],
            ],
        ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'IDEMPOTENCY_KEY_REQUIRED');
    }

    public function test_complete_sale_replays_same_key_and_payload(): void
    {
        [$operator, $store, $saleId] = $this->openCartReadyToPay();
        $this->actingAsOperatorAtStore($operator, $store);

        $key = (string) Str::uuid();
        $payload = [
            'payments' => [
                ['method' => 'pix', 'amount' => 10.00],
            ],
        ];

        $first = $this->postJson(
            "/api/operational/sales/{$saleId}/complete",
            $payload,
            ['Idempotency-Key' => $key],
        )->assertOk();

        $firstBody = $first->json();

        $second = $this->postJson(
            "/api/operational/sales/{$saleId}/complete",
            $payload,
            ['Idempotency-Key' => $key],
        )
            ->assertOk()
            ->assertHeader('Idempotent-Replayed', 'true');

        $this->assertSame($firstBody, $second->json());
        $this->assertSame(1, Sale::query()->where('id', $saleId)->where('status', SaleStatus::Completed)->count());
        $this->assertDatabaseCount('payment_lines', 1);
        $this->assertDatabaseHas('idempotency_records', [
            'scope' => 'sales.complete:'.$saleId,
            'key' => $key,
            'status' => IdempotencyRecord::STATUS_COMPLETED,
        ]);
    }

    public function test_complete_sale_same_key_different_payload_conflicts(): void
    {
        [$operator, $store, $saleId] = $this->openCartReadyToPay(amount: 20.00);
        $this->actingAsOperatorAtStore($operator, $store);

        $key = (string) Str::uuid();

        $this->postJson(
            "/api/operational/sales/{$saleId}/complete",
            [
                'payments' => [
                    ['method' => 'pix', 'amount' => 20.00],
                ],
            ],
            ['Idempotency-Key' => $key],
        )->assertOk();

        $this->postJson(
            "/api/operational/sales/{$saleId}/complete",
            [
                'payments' => [
                    [
                        'method' => 'cash',
                        'amount' => 20.00,
                        'cash_received' => 20.00,
                    ],
                ],
            ],
            ['Idempotency-Key' => $key],
        )
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'IDEMPOTENCY_KEY_REUSE');
    }

    public function test_refund_replays_same_key_and_payload(): void
    {
        $ctx = $this->completedSaleFixture();
        $key = (string) Str::uuid();
        $payload = [
            'type' => 'partial_refund',
            'reason' => 'Customer complaint',
            'lines' => [
                ['sale_line_id' => $ctx['line_id'], 'quantity' => 1],
            ],
        ];

        $first = $this->actingAs($ctx['manager'])->postJson(
            "/api/admin/sales/{$ctx['sale_id']}/refunds",
            $payload,
            ['Idempotency-Key' => $key],
        )->assertCreated();

        $second = $this->actingAs($ctx['manager'])->postJson(
            "/api/admin/sales/{$ctx['sale_id']}/refunds",
            $payload,
            ['Idempotency-Key' => $key],
        )
            ->assertCreated()
            ->assertHeader('Idempotent-Replayed', 'true');

        $this->assertSame($first->json(), $second->json());
        $this->assertDatabaseCount('refunds', 1);
    }

    public function test_refund_without_idempotency_key_returns_422(): void
    {
        $ctx = $this->completedSaleFixture();
        $this->withoutAutoIdempotencyKey();

        $this->actingAs($ctx['manager'])->postJson(
            "/api/admin/sales/{$ctx['sale_id']}/refunds",
            [
                'type' => 'partial_refund',
                'reason' => 'Missing key test',
                'lines' => [
                    ['sale_line_id' => $ctx['line_id'], 'quantity' => 1],
                ],
            ],
        )
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'IDEMPOTENCY_KEY_REQUIRED');
    }

    public function test_refund_same_key_different_payload_conflicts(): void
    {
        $ctx = $this->completedSaleFixture();
        $key = (string) Str::uuid();

        $this->actingAs($ctx['manager'])->postJson(
            "/api/admin/sales/{$ctx['sale_id']}/refunds",
            [
                'type' => 'partial_refund',
                'reason' => 'First reason text',
                'lines' => [
                    ['sale_line_id' => $ctx['line_id'], 'quantity' => 1],
                ],
            ],
            ['Idempotency-Key' => $key],
        )->assertCreated();

        $this->actingAs($ctx['manager'])->postJson(
            "/api/admin/sales/{$ctx['sale_id']}/refunds",
            [
                'type' => 'partial_refund',
                'reason' => 'Different reason text',
                'lines' => [
                    ['sale_line_id' => $ctx['line_id'], 'quantity' => 1],
                ],
            ],
            ['Idempotency-Key' => $key],
        )
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'IDEMPOTENCY_KEY_REUSE');
    }

    /**
     * @return array{0: User, 1: Store, 2: int}
     */
    private function openCartReadyToPay(float $amount = 10.00): array
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->withOpenShift($operator, $store);
        $cents = (int) round($amount * 100);
        $product = Product::factory()->create(['base_price' => $cents]);

        StoreInventory::factory()->create([
            'store_id' => $store->id,
            'product_id' => $product->id,
            'quantity' => 10,
        ]);

        $this->actingAsOperatorAtStore($operator, $store);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');
        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk();

        return [$operator, $store, $saleId];
    }

    /**
     * @return array{sale_id: int, line_id: int, manager: User}
     */
    private function completedSaleFixture(): array
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

        $product = Product::factory()->create(['base_price' => 4000]);
        StoreInventory::factory()->create([
            'store_id' => $store->id,
            'product_id' => $product->id,
            'quantity' => 4,
        ]);

        $sale = Sale::factory()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'cash_shift_id' => $shift->id,
            'status' => SaleStatus::Completed,
            'subtotal' => 4000,
            'discount_total' => 0,
            'total' => 4000,
            'completed_at' => now(),
        ]);

        $line = SaleLine::query()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 4000,
            'line_discount' => 0,
            'line_total' => 4000,
        ]);

        PaymentLine::query()->create([
            'sale_id' => $sale->id,
            'method' => PaymentMethod::Pix,
            'amount' => 4000,
            'transaction_reference' => 'stub-idem-ref',
        ]);

        return [
            'sale_id' => $sale->id,
            'line_id' => $line->id,
            'manager' => $manager,
        ];
    }
}
