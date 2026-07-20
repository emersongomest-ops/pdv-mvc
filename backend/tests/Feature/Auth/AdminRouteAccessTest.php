<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Domain\IdentityAccess\ValueObjects\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminRouteAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->getJson('/api/admin/dashboard');

        $response
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'AUTH_UNAUTHENTICATED')
            ->assertJsonPath('error.message', 'Authentication required.');
    }

    public function test_operator_cannot_access_admin_dashboard(): void
    {
        $operator = User::factory()->operator()->create();

        $response = $this->actingAs($operator)->getJson('/api/admin/dashboard');

        $response
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_ADMIN_ONLY')
            ->assertJsonPath('error.message', 'This area is restricted to managers.');
    }

    public function test_inactive_manager_cannot_access_admin_dashboard(): void
    {
        $manager = User::factory()->manager()->inactive()->create();

        $response = $this->actingAs($manager)->getJson('/api/admin/dashboard');

        $response
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_ACCOUNT_INACTIVE');
    }

    public function test_manager_can_access_admin_dashboard(): void
    {
        $manager = User::factory()->manager()->create();

        $response = $this->actingAs($manager)->getJson('/api/admin/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath('data.area', 'admin')
            ->assertJsonPath('data.user_id', $manager->id)
            ->assertJsonStructure([
                'data' => [
                    'metrics' => [
                        'products_total',
                        'products_active',
                        'products_inactive',
                        'customers_total',
                        'sales_completed',
                        'open_shifts',
                    ],
                ],
            ]);
    }
}
