<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\CashShift\ValueObjects\CashShiftStatus;
use App\Models\CashShift;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashShift>
 */
class CashShiftFactory extends Factory
{
    protected $model = CashShift::class;

    public function definition(): array
    {
        return [
            'store_id' => Store::factory(),
            'user_id' => User::factory()->operator(),
            'status' => CashShiftStatus::Open,
            'opening_cash_amount' => 10000,
            'closing_cash_amount' => null,
            'opened_at' => now(),
            'closed_at' => null,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CashShiftStatus::Closed,
            'closed_at' => now(),
            'closing_cash_amount' => 15000,
        ]);
    }
}
