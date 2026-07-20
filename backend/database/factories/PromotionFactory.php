<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Promotions\ValueObjects\DiscountType;
use App\Domain\Promotions\ValueObjects\StackingMode;
use App\Domain\Shared\Money;
use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Promotion>
 */
class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('PROMO-####')),
            'name' => fake()->words(3, true),
            'discount_type' => DiscountType::Percent,
            'discount_value' => 1000,
            'stacking_mode' => StackingMode::Accumulable,
            'applies_to_all_customers' => true,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
        ];
    }

    public function uniqueStacking(): static
    {
        return $this->state(fn (): array => ['stacking_mode' => StackingMode::Unique]);
    }

    public function forAssignedCustomers(): static
    {
        return $this->state(fn (): array => ['applies_to_all_customers' => false]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }

    public function expired(): static
    {
        return $this->state(fn (): array => [
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->subDay(),
        ]);
    }

    public function fixed(string $amount): static
    {
        return $this->state(fn (): array => [
            'discount_type' => DiscountType::Fixed,
            'discount_value' => Money::fromDecimalInput($amount),
        ]);
    }
}
