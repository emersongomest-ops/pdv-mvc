<?php

declare(strict_types=1);

namespace Tests\Feature\Store;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class StoreContextIdorTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enableStatefulApiHeaders();
    }

    public function test_operational_route_rejects_revoked_store_assignment_in_session(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $operator->stores()->attach($store);

        $this->actingAs($operator)->postJson('/api/stores/context', [
            'store_id' => $store->id,
        ])->assertOk();

        $this->postJson('/api/operational/shifts/open')->assertCreated();

        $operator->stores()->detach($store);

        $this->getJson('/api/operational/pos')
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_STORE_ACCESS_DENIED');
    }

    public function test_operational_route_rejects_inactive_store_in_session(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->withOpenShift($operator, $store);

        $this
            ->actingAsOperatorAtStore($operator, $store)
            ->getJson('/api/operational/pos')
            ->assertOk();

        $store->update(['is_active' => false]);

        $this->getJson('/api/operational/pos')
            ->assertForbidden()
            ->assertJsonPath('error.code', 'STORE_INACTIVE');
    }
}
