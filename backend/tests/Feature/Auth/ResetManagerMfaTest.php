<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\AuditLog;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\ActsWithAdminStoreAccess;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class ResetManagerMfaTest extends TestCase
{
    use ActsWithAdminStoreAccess;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    private const SECRET = 'JBSWY3DPEHPK3PXP';

    #[Test]
    public function manager_can_reset_another_manager_mfa_and_audit(): void
    {
        $store = Store::factory()->create();
        $actor = User::factory()->manager()->withMfa(self::SECRET)->create();
        $target = User::factory()->manager()->withMfa(self::SECRET)->create([
            'email' => 'locked-out@pos.test',
        ]);
        $this->attachManagerToStore($actor, $store);
        $this->attachManagerToStore($target, $store);

        DB::table('sessions')->insert([
            'id' => 'sess-target-1',
            'user_id' => $target->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'test',
            'payload' => 'x',
            'last_activity' => time(),
        ]);

        $this->assertTrue($target->fresh()->hasMfaEnabled());

        $this->actingAsManagerForStore($actor, $store)
            ->postJson("/api/admin/users/{$target->id}/mfa/reset", [
                'reason' => 'Lost authenticator device',
            ])
            ->assertOk()
            ->assertJsonPath('data.user.mfa_enabled', false)
            ->assertJsonPath('data.message', 'Manager MFA reset. Target must re-enroll on next login.');

        $target->refresh();
        $this->assertFalse($target->hasMfaEnabled());
        $this->assertNull($target->mfa_secret);
        $this->assertNull($target->mfa_confirmed_at);
        $this->assertNull($target->mfa_recovery_codes);
        $this->assertSame(0, DB::table('sessions')->where('user_id', $target->id)->count());

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'identity.mfa_reset',
            'actor_user_id' => $actor->id,
            'subject_type' => 'user',
            'subject_id' => $target->id,
        ]);

        $log = AuditLog::query()->where('action', 'identity.mfa_reset')->firstOrFail();
        $this->assertSame('Lost authenticator device', $log->metadata['reason'] ?? null);
    }

    #[Test]
    public function manager_cannot_reset_own_mfa(): void
    {
        $store = Store::factory()->create();
        $manager = User::factory()->manager()->withMfa(self::SECRET)->create();

        $this->actingAsManagerForStore($manager, $store)
            ->postJson("/api/admin/users/{$manager->id}/mfa/reset", [
                'reason' => 'Trying self reset',
            ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'AUTH_CANNOT_RESET_OWN_MFA');
    }

    #[Test]
    public function mfa_reset_rejects_operator_targets(): void
    {
        $store = Store::factory()->create();
        $manager = User::factory()->manager()->withMfa(self::SECRET)->create();
        $operator = User::factory()->operator()->create();
        $this->attachManagerToStore($manager, $store);
        $operator->stores()->attach($store);

        $this->actingAsManagerForStore($manager, $store)
            ->postJson("/api/admin/users/{$operator->id}/mfa/reset", [
                'reason' => 'Wrong role',
            ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'AUTH_MFA_RESET_NOT_APPLICABLE');
    }

    #[Test]
    public function mfa_reset_requires_reason(): void
    {
        $store = Store::factory()->create();
        $actor = User::factory()->manager()->withMfa(self::SECRET)->create();
        $target = User::factory()->manager()->withMfa(self::SECRET)->create();

        $this->actingAsManagerForStore($actor, $store)
            ->postJson("/api/admin/users/{$target->id}/mfa/reset", [])
            ->assertStatus(422);
    }

    #[Test]
    public function after_reset_login_requires_mfa_setup(): void
    {
        $store = Store::factory()->create();
        $actor = User::factory()->manager()->withMfa(self::SECRET)->create();
        $target = User::factory()->manager()->withMfa(self::SECRET)->create([
            'email' => 'reset-me@pos.test',
            'password' => 'secret-password',
        ]);
        $this->attachManagerToStore($actor, $store);

        $this->actingAsManagerForStore($actor, $store)
            ->postJson("/api/admin/users/{$target->id}/mfa/reset", [
                'reason' => 'Break-glass unlock',
            ])
            ->assertOk();

        $this->enableStatefulApiHeaders();
        $this->postJson('/api/auth/login', [
            'email' => 'reset-me@pos.test',
            'password' => 'secret-password',
        ])
            ->assertOk()
            ->assertJsonPath('data.mfa_required', true)
            ->assertJsonPath('data.mfa_setup_required', true);
    }
}
