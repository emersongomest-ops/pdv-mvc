<?php

declare(strict_types=1);

namespace Tests\Feature\CashShift;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class OperationalShiftGateTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    public function test_operational_pos_without_open_shift_returns_shift_not_open(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();

        $response = $this
            ->actingAsOperatorAtStore($operator, $store)
            ->getJson('/api/operational/pos');

        $response
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'SHIFT_NOT_OPEN');
    }

    public function test_operational_pos_with_open_shift_returns_shift_id(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $shift = $this->withOpenShift($operator, $store);

        $response = $this
            ->actingAsOperatorAtStore($operator, $store)
            ->getJson('/api/operational/pos');

        $response
            ->assertOk()
            ->assertJsonPath('data.shift_id', $shift->id)
            ->assertJsonPath('data.store_id', $store->id);
    }

    public function test_operational_pos_with_shift_at_wrong_store_returns_shift_store_mismatch(): void
    {
        $storeA = Store::factory()->create();
        $storeB = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $operator->stores()->attach([$storeA->id, $storeB->id]);
        $this->withOpenShift($operator, $storeA);

        $response = $this
            ->actingAsOperatorAtStore($operator, $storeB)
            ->getJson('/api/operational/pos');

        $response
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'SHIFT_STORE_MISMATCH');
    }
}
