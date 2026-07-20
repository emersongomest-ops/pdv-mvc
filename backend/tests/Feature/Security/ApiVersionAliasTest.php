<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class ApiVersionAliasTest extends TestCase
{
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enableStatefulApiHeaders();
    }

    #[Test]
    public function v1_login_mirrors_unversioned_route(): void
    {
        User::factory()->operator()->create([
            'email' => 'v1-alias@pos.test',
            'password' => 'correct-password',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'v1-alias@pos.test',
            'password' => 'correct-password',
        ])
            ->assertOk()
            ->assertJsonPath('data.user.email', 'v1-alias@pos.test');
    }

    #[Test]
    public function v1_me_requires_auth_like_unversioned(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'AUTH_UNAUTHENTICATED');

        $user = User::factory()->operator()->create();

        $this->actingAs($user)
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.user.id', $user->id);
    }
}
