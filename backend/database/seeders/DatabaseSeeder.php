<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DemoStoreSeeder::class,
            OperatorUserSeeder::class,
            ManagerUserSeeder::class,
            DemoCatalogSeeder::class,
            DemoInventorySeeder::class,
            DemoCustomerSeeder::class,
            DemoPromotionSeeder::class,
        ]);
    }
}
