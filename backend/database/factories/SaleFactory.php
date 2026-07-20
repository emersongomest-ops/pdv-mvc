<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Sales\ValueObjects\SaleStatus;
use App\Models\CashShift;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_id' => Store::factory(),
            'user_id' => User::factory(),
            'cash_shift_id' => CashShift::factory(),
            'status' => SaleStatus::InProgress,
            'subtotal' => 0,
            'discount_total' => 0,
            'total' => 0,
            'completed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (): array => [
            'status' => SaleStatus::Completed,
            'completed_at' => now(),
        ]);
    }

    public function held(?string $label = 'Table 5'): static
    {
        return $this->state(fn (): array => [
            'status' => SaleStatus::Held,
            'hold_label' => $label,
            'held_at' => now(),
        ]);
    }
}
