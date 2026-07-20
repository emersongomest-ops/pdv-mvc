<?php

declare(strict_types=1);

namespace Tests\Feature\Shared;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class CartIdempotencyTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    public function test_create_sale_without_idempotency_key_returns_422(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->withOpenShift($operator, $store);
        $this->actingAsOperatorAtStore($operator, $store);
        $this->withoutAutoIdempotencyKey();

        $this->postJson('/api/operational/sales')
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'IDEMPOTENCY_KEY_REQUIRED');
    }

    public function test_create_sale_replays_same_key_and_payload(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->withOpenShift($operator, $store);
        $this->actingAsOperatorAtStore($operator, $store);

        $key = (string) Str::uuid();
        $payload = ['product_id' => Product::factory()->create(['base_price' => 1000])->id, 'quantity' => 1];

        $first = $this->postJson('/api/operational/sales', $payload, ['Idempotency-Key' => $key])
            ->assertCreated();

        $second = $this->postJson('/api/operational/sales', $payload, ['Idempotency-Key' => $key])
            ->assertCreated()
            ->assertHeader('Idempotent-Replayed', 'true');

        $this->assertSame($first->json(), $second->json());
        $this->assertSame(1, Sale::query()->count());
        $this->assertSame(1, SaleLine::query()->count());
    }

    public function test_add_line_replays_same_key_and_payload(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->withOpenShift($operator, $store);
        $this->actingAsOperatorAtStore($operator, $store);

        $product = Product::factory()->create(['base_price' => 1500]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $key = (string) Str::uuid();
        $payload = ['product_id' => $product->id, 'quantity' => 2];

        $first = $this->postJson(
            "/api/operational/sales/{$saleId}/lines",
            $payload,
            ['Idempotency-Key' => $key],
        )->assertOk();

        $second = $this->postJson(
            "/api/operational/sales/{$saleId}/lines",
            $payload,
            ['Idempotency-Key' => $key],
        )
            ->assertOk()
            ->assertHeader('Idempotent-Replayed', 'true');

        $this->assertSame($first->json(), $second->json());
        $this->assertSame(1, SaleLine::query()->where('sale_id', $saleId)->count());
        $this->assertSame(2, (int) SaleLine::query()->where('sale_id', $saleId)->value('quantity'));
    }

    public function test_add_line_same_key_different_payload_conflicts(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->withOpenShift($operator, $store);
        $this->actingAsOperatorAtStore($operator, $store);

        $productA = Product::factory()->create(['base_price' => 1000]);
        $productB = Product::factory()->create(['base_price' => 2000]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');
        $key = (string) Str::uuid();

        $this->postJson(
            "/api/operational/sales/{$saleId}/lines",
            ['product_id' => $productA->id, 'quantity' => 1],
            ['Idempotency-Key' => $key],
        )->assertOk();

        $this->postJson(
            "/api/operational/sales/{$saleId}/lines",
            ['product_id' => $productB->id, 'quantity' => 1],
            ['Idempotency-Key' => $key],
        )
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'IDEMPOTENCY_KEY_REUSE');
    }
}
