<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\IdentityAccess\ValueObjects\UserRole;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class ManagerUserSeeder extends Seeder
{
    /** Demo TOTP secret — document in README; not for production. */
    public const DEMO_MFA_SECRET = 'JBSWY3DPEHPK3PXP';

    public function run(): void
    {
        $store = Store::query()->where('code', 'MAIN')->first();

        if ($store === null) {
            throw new RuntimeException('Demo store MAIN missing — run DemoStoreSeeder first.');
        }

        $user = User::query()->firstOrCreate(
            ['email' => 'manager@pos.test'],
            [
                'name' => 'Demo Manager',
                'password' => 'password',
                'role' => UserRole::Manager,
                'is_active' => true,
            ],
        );

        // Always re-apply demo MFA with the current APP_KEY (Docker may rotate keys until pinned).
        $user->forceFill([
            'mfa_secret' => self::DEMO_MFA_SECRET,
            'mfa_confirmed_at' => now(),
            'mfa_last_otp_timestamp' => null,
        ])->save();

        $user->stores()->syncWithoutDetaching([$store->id]);
    }
}
