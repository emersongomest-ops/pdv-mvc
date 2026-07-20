<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;

class DemoStoreSeeder extends Seeder
{
    public function run(): void
    {
        Store::query()->firstOrCreate(
            ['code' => 'MAIN'],
            ['name' => 'Main Store', 'is_active' => true],
        );
    }
}
