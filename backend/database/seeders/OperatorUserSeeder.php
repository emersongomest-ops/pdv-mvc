<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\IdentityAccess\ValueObjects\UserRole;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class OperatorUserSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::query()->where('code', 'MAIN')->first();

        if ($store === null) {
            throw new RuntimeException('Demo store MAIN missing — run DemoStoreSeeder first.');
        }

        $user = User::query()->firstOrCreate(
            ['email' => 'operator@pos.test'],
            [
                'name' => 'Demo Operator',
                'password' => 'password',
                'role' => UserRole::Operator,
                'is_active' => true,
            ],
        );

        $user->stores()->syncWithoutDetaching([$store->id]);
    }
}
