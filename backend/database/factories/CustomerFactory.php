<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###########'),
            'phone' => fake()->numerify('119########'),
            'birth_date' => fake()->date(),
            'address' => fake()->streetAddress().', '.fake()->city().' - SP',
            'lifetime_spend' => 0,
        ];
    }
}
