<?php

declare(strict_types=1);

namespace App\Application\Promotions\Support;

use App\Models\Promotion;

final class PromotionAuditSnapshot
{
    /**
     * @return array{
     *     code: string,
     *     name: string,
     *     discount_type: string,
     *     discount_value: int,
     *     stacking_mode: string,
     *     applies_to_all_customers: bool,
     *     is_active: bool,
     *     starts_at: string|null,
     *     ends_at: string|null,
     *     customer_ids: list<int>
     * }
     */
    public static function from(Promotion $promotion): array
    {
        $promotion->loadMissing('customers');

        return [
            'code' => $promotion->code,
            'name' => $promotion->name,
            'discount_type' => $promotion->discount_type->value,
            'discount_value' => (int) $promotion->discount_value,
            'stacking_mode' => $promotion->stacking_mode->value,
            'applies_to_all_customers' => (bool) $promotion->applies_to_all_customers,
            'is_active' => (bool) $promotion->is_active,
            'starts_at' => $promotion->starts_at?->toIso8601String(),
            'ends_at' => $promotion->ends_at?->toIso8601String(),
            'customer_ids' => $promotion->customers
                ->pluck('id')
                ->map(static fn ($id): int => (int) $id)
                ->values()
                ->all(),
        ];
    }
}
