<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Domain\IdentityAccess\Services\MfaRecoveryCodeVault;
use App\Domain\IdentityAccess\Services\TotpAuthenticatorInterface;
use App\Models\User;
use Database\Seeders\DemoStoreSeeder;
use Database\Seeders\ManagerUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class ManagerMfaTest extends TestCase
{
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    private const SECRET = 'JBSWY3DPEHPK3PXP';

    protected function setUp(): void
    {
        parent::setUp();
        $this->enableStatefulApiHeaders();
    }

    #[Test]
    public function manager_login_requires_mfa_and_blocks_admin_until_verified(): void
    {
        $manager = User::factory()->manager()->withMfa(self::SECRET)->create([
            'email' => 'manager@pos.test',
            'password' => 'secret-password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'manager@pos.test',
            'password' => 'secret-password',
        ])
            ->assertOk()
            ->assertJsonPath('data.mfa_required', true)
            ->assertJsonPath('data.mfa_setup_required', false)
            ->assertJsonPath('data.user.id', $manager->id);

        $this->getJson('/api/auth/me')->assertUnauthorized();
        $this->getJson('/api/admin/dashboard')->assertUnauthorized();

        $code = $this->app->make(TotpAuthenticatorInterface::class)->currentOtp(self::SECRET);

        $this->postJson('/api/auth/mfa/verify', ['code' => $code])
            ->assertOk()
            ->assertJsonPath('data.mfa_required', false)
            ->assertJsonPath('data.user.role', 'manager');

        $this->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('data.user.id', $manager->id);

        $this->getJson('/api/admin/dashboard')->assertOk();
    }

    #[Test]
    public function manager_without_mfa_must_enroll_before_session(): void
    {
        User::factory()->manager()->create([
            'email' => 'new-manager@pos.test',
            'password' => 'secret-password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'new-manager@pos.test',
            'password' => 'secret-password',
        ])
            ->assertOk()
            ->assertJsonPath('data.mfa_required', true)
            ->assertJsonPath('data.mfa_setup_required', true);

        $setup = $this->postJson('/api/auth/mfa/setup')->assertOk();
        $secret = $setup->json('data.secret');
        $this->assertIsString($secret);
        $this->assertNotSame('', $secret);
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $setup->json('data.qr_data_uri'));

        $code = $this->app->make(TotpAuthenticatorInterface::class)->currentOtp($secret);

        $this->postJson('/api/auth/mfa/confirm', ['code' => $code])
            ->assertOk()
            ->assertJsonPath('data.mfa_required', false)
            ->assertJsonCount(8, 'data.recovery_codes');

        $this->getJson('/api/admin/dashboard')->assertOk();
    }

    #[Test]
    public function manager_can_verify_with_one_time_recovery_code(): void
    {
        $vault = new MfaRecoveryCodeVault;
        $plains = $vault->generatePlaintexts();

        User::factory()->manager()->withMfa(self::SECRET)->create([
            'email' => 'manager@pos.test',
            'password' => 'secret-password',
            'mfa_recovery_codes' => $vault->hashAll($plains),
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'manager@pos.test',
            'password' => 'secret-password',
        ])->assertOk();

        $this->postJson('/api/auth/mfa/verify', ['code' => $plains[0]])
            ->assertOk()
            ->assertJsonPath('data.user.role', 'manager');

        $this->postJson('/api/auth/logout')->assertOk();

        $this->postJson('/api/auth/login', [
            'email' => 'manager@pos.test',
            'password' => 'secret-password',
        ])->assertOk();

        $this->postJson('/api/auth/mfa/verify', ['code' => $plains[0]])
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'AUTH_MFA_INVALID_CODE');
    }

    #[Test]
    public function invalid_mfa_code_returns_auth_mfa_invalid_code(): void
    {
        User::factory()->manager()->withMfa(self::SECRET)->create([
            'email' => 'manager@pos.test',
            'password' => 'secret-password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'manager@pos.test',
            'password' => 'secret-password',
        ])->assertOk();

        $this->postJson('/api/auth/mfa/verify', ['code' => '000000'])
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'AUTH_MFA_INVALID_CODE');
    }

    #[Test]
    public function mfa_endpoints_require_pending_challenge(): void
    {
        $this->postJson('/api/auth/mfa/verify', ['code' => '123456'])
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'AUTH_MFA_NOT_PENDING');

        $this->postJson('/api/auth/mfa/setup')
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'AUTH_MFA_NOT_PENDING');
    }

    #[Test]
    public function operator_login_skips_mfa(): void
    {
        User::factory()->operator()->create([
            'email' => 'operator@pos.test',
            'password' => 'secret-password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'operator@pos.test',
            'password' => 'secret-password',
        ])
            ->assertOk()
            ->assertJsonPath('data.mfa_required', false)
            ->assertJsonPath('data.user.role', 'operator');

        $this->getJson('/api/auth/me')->assertOk();
    }

    #[Test]
    public function demo_manager_seeder_enables_known_mfa_secret(): void
    {
        $this->seed([DemoStoreSeeder::class, ManagerUserSeeder::class]);

        $manager = User::query()->where('email', 'manager@pos.test')->first();
        $this->assertNotNull($manager);
        $this->assertTrue($manager->hasMfaEnabled());
        $this->assertSame(ManagerUserSeeder::DEMO_MFA_SECRET, $manager->mfa_secret);
    }
}
