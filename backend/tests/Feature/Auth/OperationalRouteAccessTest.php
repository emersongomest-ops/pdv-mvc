<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class OperationalRouteAccessTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enableStatefulApiHeaders();
    }

    public function test_guest_cannot_access_operational_pos(): void
    {
        $response = $this->getJson('/api/operational/pos');

        $response
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'AUTH_UNAUTHENTICATED');
    }

    public function test_operator_can_access_operational_pos(): void
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
            ->assertJsonPath('data.user_id', $operator->id);
    }

    public function test_manager_can_access_operational_pos(): void
    {
        $store = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $this->withOpenShift($manager, $store);

        $response = $this
            ->actingAsOperatorAtStore($manager, $store)
            ->getJson('/api/operational/pos');

        $response
            ->assertOk()
            ->assertJsonPath('data.area', 'operational')
            ->assertJsonPath('data.user_id', $manager->id);
    }

    public function test_inactive_operator_cannot_access_operational_pos(): void
    {
        $operator = User::factory()->operator()->inactive()->create();

        $response = $this->actingAs($operator)->getJson('/api/operational/pos');

        $response
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_ACCOUNT_INACTIVE');
    }
}
