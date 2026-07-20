<?php

declare(strict_types=1);

namespace Tests\Feature\Store;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class OperationalStoreContextTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enableStatefulApiHeaders();
    }

    public function test_operational_pos_without_store_context_returns_auth_store_context_required(): void
    {
        $operator = User::factory()->operator()->create();

        $response = $this->actingAs($operator)->getJson('/api/operational/pos');

        $response
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'AUTH_STORE_CONTEXT_REQUIRED')
            ->assertJsonPath('error.message', 'Store context must be selected.');
    }

    public function test_operational_pos_with_store_context_but_no_shift_returns_shift_not_open(): void
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

    public function test_operational_pos_with_store_context_and_open_shift_returns_store_id(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->withOpenShift($operator, $store);

        $response = $this
            ->actingAsOperatorAtStore($operator, $store)
            ->getJson('/api/operational/pos');

        $response
            ->assertOk()
            ->assertJsonPath('data.area', 'operational')
            ->assertJsonPath('data.store_id', $store->id);
    }

    public function test_operational_pos_after_selecting_store_context_in_session_flow(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $operator->stores()->attach($store);

        $this->actingAs($operator)->postJson('/api/stores/context', [
            'store_id' => $store->id,
        ])->assertOk();

        $this->postJson('/api/operational/shifts/open')->assertCreated();

        $this->getJson('/api/operational/pos')
            ->assertOk()
            ->assertJsonPath('data.store_id', $store->id);
    }
}
