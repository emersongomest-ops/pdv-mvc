<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use App\Domain\IdentityAccess\ValueObjects\UserRole;
use App\Models\Store;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ManagerUserSeeder;
use Database\Seeders\OperatorUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

final class DemoUserSeedersTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function database_seeder_creates_one_user_per_role_on_main_store(): void
    {
        $this->seed(DatabaseSeeder::class);

        $store = Store::query()->where('code', 'MAIN')->first();
        $this->assertNotNull($store);

        $operator = User::query()->where('email', 'operator@pos.test')->first();
        $this->assertNotNull($operator);
        $this->assertSame(UserRole::Operator, $operator->role);
        $this->assertTrue($operator->is_active);
        $this->assertTrue($operator->stores->contains($store));

        $manager = User::query()->where('email', 'manager@pos.test')->first();
        $this->assertNotNull($manager);
        $this->assertSame(UserRole::Manager, $manager->role);
        $this->assertTrue($manager->is_active);
        $this->assertTrue($manager->stores->contains($store));
        $this->assertTrue($manager->hasMfaEnabled());
    }

    #[Test]
    public function database_seeder_is_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(1, Store::query()->where('code', 'MAIN')->count());
        $this->assertSame(1, User::query()->where('email', 'operator@pos.test')->count());
        $this->assertSame(1, User::query()->where('email', 'manager@pos.test')->count());
    }

    #[Test]
    public function operator_seeder_fails_without_demo_store(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Demo store MAIN missing');

        $this->seed(OperatorUserSeeder::class);
    }

    #[Test]
    public function manager_seeder_fails_without_demo_store(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Demo store MAIN missing');

        $this->seed(ManagerUserSeeder::class);
    }
}
