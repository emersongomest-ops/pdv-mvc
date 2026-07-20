<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\IdentityAccess\ValueObjects\UserRole;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        // Bypass Eloquent encrypted casts: legacy ciphertext from a rotated APP_KEY
        // would throw "MAC is invalid" on attribute access during forceFill/save.
        DB::table('users')->where('id', $user->id)->update([
            'mfa_secret' => encrypt(self::DEMO_MFA_SECRET),
            'mfa_confirmed_at' => now(),
            'mfa_last_otp_timestamp' => null,
            'mfa_recovery_codes' => null,
        ]);

        $user->stores()->syncWithoutDetaching([$store->id]);
    }
}
