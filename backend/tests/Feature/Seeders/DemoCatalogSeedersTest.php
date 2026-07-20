<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerStoreStat;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Support\Pii\PiiCrypto;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoCatalogSeeder;
use Database\Seeders\DemoInventorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

final class DemoCatalogSeedersTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function database_seeder_fills_catalog_inventory_customers_and_promotions(): void
    {
        $this->seed(DatabaseSeeder::class);

        $store = Store::query()->where('code', 'MAIN')->firstOrFail();

        $this->assertSame(3, Category::query()->count());
        $this->assertSame(6, Product::query()->where('sku', 'like', 'DEMO-%')->count());
        $this->assertSame(6, StoreInventory::query()->where('store_id', $store->id)->count());
        $this->assertSame(3, Customer::query()->count());
        $this->assertTrue(
            Customer::query()
                ->where('cpf_hash', PiiCrypto::blindIndex('39053344705'))
                ->exists(),
        );
        $this->assertSame(2, CustomerStoreStat::query()->where('store_id', $store->id)->count());
        $this->assertTrue(Promotion::query()->where('code', 'WELCOME10')->exists());
        $this->assertTrue(Promotion::query()->where('code', 'VIP5OFF')->exists());

        $vip = Promotion::query()->where('code', 'VIP5OFF')->firstOrFail();
        $this->assertFalse($vip->applies_to_all_customers);
        $this->assertTrue(
            $vip->customers()
                ->where('cpf_hash', PiiCrypto::blindIndex('39053344705'))
                ->exists(),
        );
    }

    #[Test]
    public function rich_demo_seed_is_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(3, Category::query()->count());
        $this->assertSame(6, Product::query()->where('sku', 'like', 'DEMO-%')->count());
        $this->assertSame(6, StoreInventory::query()->count());
        $this->assertSame(3, Customer::query()->count());
        $this->assertSame(2, Promotion::query()->whereIn('code', ['WELCOME10', 'VIP5OFF'])->count());
    }

    #[Test]
    public function inventory_seeder_fails_without_catalog(): void
    {
        $this->seed(\Database\Seeders\DemoStoreSeeder::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Demo catalog products missing');

        $this->seed(DemoInventorySeeder::class);
    }

    #[Test]
    public function inventory_seeder_fails_without_store(): void
    {
        $this->seed(DemoCatalogSeeder::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Demo store MAIN missing');

        $this->seed(DemoInventorySeeder::class);
    }
}
