<?php

declare(strict_types=1);

namespace Tests\Feature\CashShift;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class CloseCashShiftTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    public function test_operator_can_close_open_shift(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $shift = $this->withOpenShift($operator, $store);

        $response = $this
            ->actingAsOperatorAtStore($operator, $store)
            ->postJson('/api/operational/shifts/close', [
                'closing_cash_amount' => 200.00,
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.message', 'Cash shift closed.')
            ->assertJsonPath('data.shift.id', $shift->id)
            ->assertJsonPath('data.shift.status', 'closed')
            ->assertJsonPath('data.shift.closing_cash_amount', '200.00');
    }

    public function test_closing_without_open_shift_returns_shift_not_open(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();

        $response = $this
            ->actingAsOperatorAtStore($operator, $store)
            ->postJson('/api/operational/shifts/close');

        $response
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'SHIFT_NOT_OPEN');
    }

    public function test_current_shift_returns_null_when_none_open(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();

        $this
            ->actingAsOperatorAtStore($operator, $store)
            ->getJson('/api/operational/shifts/current')
            ->assertOk()
            ->assertJsonPath('data.shift', null);
    }

    public function test_current_shift_returns_open_shift(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $shift = $this->withOpenShift($operator, $store);

        $this
            ->actingAsOperatorAtStore($operator, $store)
            ->getJson('/api/operational/shifts/current')
            ->assertOk()
            ->assertJsonPath('data.shift.id', $shift->id)
            ->assertJsonPath('data.shift.status', 'open');
    }
}
