<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class LoginTest extends TestCase
{
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enableStatefulApiHeaders();
    }

    public function test_manager_can_login_and_access_admin_route(): void
    {
        $manager = User::factory()->manager()->create([
            'email' => 'manager@pos.test',
            'password' => 'secret-password',
        ]);

        $login = $this->postJson('/api/auth/login', [
            'email' => 'manager@pos.test',
            'password' => 'secret-password',
        ]);

        $login
            ->assertOk()
            ->assertJsonPath('data.user.role', 'manager')
            ->assertJsonPath('data.user.email', 'manager@pos.test');

        $dashboard = $this->getJson('/api/admin/dashboard');

        $dashboard
            ->assertOk()
            ->assertJsonPath('data.area', 'admin');
    }

    public function test_operator_can_login_and_access_operational_route(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create([
            'email' => 'operator@pos.test',
            'password' => 'secret-password',
        ]);
        $operator->stores()->attach($store);

        $this->postJson('/api/auth/login', [
            'email' => 'operator@pos.test',
            'password' => 'secret-password',
        ])->assertOk()->assertJsonPath('data.user.role', 'operator');

        $this->postJson('/api/stores/context', [
            'store_id' => $store->id,
        ])->assertOk();

        $this->postJson('/api/operational/shifts/open')->assertCreated();

        $this->getJson('/api/operational/pos')
            ->assertOk()
            ->assertJsonPath('data.area', 'operational')
            ->assertJsonPath('data.store_id', $store->id);
    }

    public function test_login_with_invalid_credentials_returns_auth_invalid_credentials(): void
    {
        User::factory()->operator()->create([
            'email' => 'operator@pos.test',
            'password' => 'secret-password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'operator@pos.test',
            'password' => 'wrong-password',
        ]);

        $response
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'AUTH_INVALID_CREDENTIALS');
    }

    public function test_inactive_user_login_returns_auth_account_inactive(): void
    {
        User::factory()->operator()->inactive()->create([
            'email' => 'inactive@pos.test',
            'password' => 'secret-password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'inactive@pos.test',
            'password' => 'secret-password',
        ]);

        $response
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_ACCOUNT_INACTIVE');
    }
}
