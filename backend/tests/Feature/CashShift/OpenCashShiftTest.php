<?php

declare(strict_types=1);

namespace Tests\Feature\CashShift;

use App\Models\CashShift;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class OpenCashShiftTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    public function test_operator_can_open_shift_at_current_store(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();

        $response = $this
            ->actingAsOperatorAtStore($operator, $store)
            ->postJson('/api/operational/shifts/open', [
                'opening_cash_amount' => 150.50,
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.message', 'Cash shift opened.')
            ->assertJsonPath('data.shift.store_id', $store->id)
            ->assertJsonPath('data.shift.status', 'open')
            ->assertJsonPath('data.shift.opening_cash_amount', '150.50');

        $this->assertDatabaseHas('cash_shifts', [
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'status' => 'open',
        ]);
    }

    public function test_opening_second_shift_returns_shift_already_open(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->withOpenShift($operator, $store);

        $response = $this
            ->actingAsOperatorAtStore($operator, $store)
            ->postJson('/api/operational/shifts/open');

        $response
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'SHIFT_ALREADY_OPEN');
    }

    public function test_opening_shift_at_different_store_while_one_is_open_returns_shift_already_open(): void
    {
        $storeA = Store::factory()->create();
        $storeB = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $operator->stores()->attach([$storeA->id, $storeB->id]);
        $this->withOpenShift($operator, $storeA);

        $response = $this
            ->actingAsOperatorAtStore($operator, $storeB)
            ->postJson('/api/operational/shifts/open');

        $response
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'SHIFT_ALREADY_OPEN');
    }
}
