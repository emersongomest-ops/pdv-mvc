<?php

declare(strict_types=1);

namespace App\Domain\Promotions\Support;

use App\Domain\Promotions\ValueObjects\DiscountType;
use App\Domain\Shared\Money;
use App\Models\Promotion;
use Illuminate\Support\Collection;

final class PromotionDiscountCalculator
{
    /**
     * @param Collection<int, Promotion> $promotions
     * @return array{discount_total: int, amounts: array<int, int>}
     */
    public static function calculate(int $subtotalCents, Collection $promotions): array
    {
        $amounts = [];
        $rawTotal = 0;

        foreach ($promotions as $promotion) {
            $amount = self::amountFor($subtotalCents, $promotion);
            $amounts[$promotion->id] = $amount;
            $rawTotal = Money::add($rawTotal, $amount);
        }

        if ($rawTotal > $subtotalCents) {
            $amounts = self::scaleDown($amounts, $rawTotal, $subtotalCents);
            $rawTotal = $subtotalCents;
        }

        return [
            'discount_total' => $rawTotal,
            'amounts' => $amounts,
        ];
    }

    public static function amountFor(int $subtotalCents, Promotion $promotion): int
    {
        $value = (int) $promotion->discount_value;

        if ($promotion->discount_type === DiscountType::Fixed) {
            return $value > $subtotalCents ? $subtotalCents : $value;
        }

        return Money::percentOf($subtotalCents, $value);
    }

    /**
     * @param array<int, int> $amounts
     * @return array<int, int>
     */
    private static function scaleDown(array $amounts, int $rawTotal, int $subtotalCents): array
    {
        if ($rawTotal === 0) {
            return $amounts;
        }

        $scaled = [];
        $allocated = 0;
        $ids = array_keys($amounts);
        $lastId = end($ids);

        foreach ($amounts as $id => $amount) {
            if ($id === $lastId) {
                $scaled[$id] = Money::sub($subtotalCents, $allocated);

                continue;
            }

            $portion = intdiv($amount * $subtotalCents, $rawTotal);
            $scaled[$id] = $portion;
            $allocated = Money::add($allocated, $portion);
        }

        return $scaled;
    }
}
