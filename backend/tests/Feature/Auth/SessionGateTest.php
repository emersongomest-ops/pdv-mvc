<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Domain\IdentityAccess\Services\TotpAuthenticatorInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class SessionGateTest extends TestCase
{
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enableStatefulApiHeaders();
    }

    #[Test]
    public function guest_cannot_read_current_user(): void
    {
        $this->getJson('/api/auth/me')->assertUnauthorized();
    }

    #[Test]
    public function authenticated_user_can_read_current_user(): void
    {
        $user = User::factory()->operator()->create([
            'email' => 'operator@pos.test',
            'password' => 'secret-password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'operator@pos.test',
            'password' => 'secret-password',
        ])->assertOk();

        $this->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('data.user.id', $user->id)
            ->assertJsonPath('data.user.email', 'operator@pos.test')
            ->assertJsonPath('data.user.role', 'operator');
    }

    #[Test]
    public function logout_invalidates_session(): void
    {
        User::factory()->manager()->withMfa('JBSWY3DPEHPK3PXP')->create([
            'email' => 'manager@pos.test',
            'password' => 'secret-password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'manager@pos.test',
            'password' => 'secret-password',
        ])->assertOk();

        $code = $this->app->make(TotpAuthenticatorInterface::class)
            ->currentOtp('JBSWY3DPEHPK3PXP');

        $this->postJson('/api/auth/mfa/verify', ['code' => $code])->assertOk();

        $this->postJson('/api/auth/logout')
            ->assertOk()
            ->assertJsonPath('data.logged_out', true);

        $this->getJson('/api/auth/me')->assertUnauthorized();
    }

    #[Test]
    public function inactive_authenticated_user_is_rejected_on_me(): void
    {
        $user = User::factory()->operator()->create([
            'email' => 'soon-inactive@pos.test',
            'password' => 'secret-password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'soon-inactive@pos.test',
            'password' => 'secret-password',
        ])->assertOk();

        $user->forceFill(['is_active' => false])->save();

        $this->getJson('/api/auth/me')
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_ACCOUNT_INACTIVE');
    }
}
