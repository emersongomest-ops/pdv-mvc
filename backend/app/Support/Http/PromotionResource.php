<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Domain\Shared\Money;
use App\Models\Customer;
use App\Models\Promotion;
use App\Models\SalePromotion;

final class PromotionResource
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(Promotion $promotion): array
    {
        $promotion->loadMissing('customers');

        return [
            'id' => $promotion->id,
            'code' => $promotion->code,
            'name' => $promotion->name,
            'discount_type' => $promotion->discount_type->value,
            'discount_value' => Money::toDecimalString((int) $promotion->discount_value),
            'stacking_mode' => $promotion->stacking_mode->value,
            'applies_to_all_customers' => $promotion->applies_to_all_customers,
            'is_active' => $promotion->is_active,
            'starts_at' => $promotion->starts_at?->toIso8601String(),
            'ends_at' => $promotion->ends_at?->toIso8601String(),
            'customer_ids' => $promotion->customers->map(fn (Customer $c) => $c->id)->values()->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function appliedToArray(SalePromotion $applied): array
    {
        $applied->loadMissing('promotion');

        return [
            'promotion_id' => $applied->promotion_id,
            'code' => $applied->promotion?->code,
            'discount_amount' => Money::toDecimalString((int) $applied->discount_amount),
            'stacking_mode' => $applied->promotion?->stacking_mode->value,
        ];
    }
}
