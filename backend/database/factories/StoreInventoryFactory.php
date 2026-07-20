<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\Store;
use App\Models\StoreInventory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StoreInventory>
 */
class StoreInventoryFactory extends Factory
{
    protected $model = StoreInventory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_id' => Store::factory(),
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(10, 100),
            'track_stock' => true,
        ];
    }

    public function untracked(): static
    {
        return $this->state(fn (): array => ['track_stock' => false]);
    }
}
