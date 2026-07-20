<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Domain\Shared\Money;
use App\Models\Refund;
use App\Models\RefundLine;

final class RefundResource
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(Refund $refund): array
    {
        $refund->loadMissing(['lines', 'user']);

        return [
            'id' => $refund->id,
            'sale_id' => $refund->sale_id,
            'store_id' => $refund->store_id,
            'user_id' => $refund->user_id,
            'operator_name' => $refund->user?->name,
            'type' => $refund->type->value,
            'reason' => $refund->reason,
            'amount' => Money::toDecimalString((int) $refund->amount),
            'payment_refund_reference' => $refund->payment_refund_reference,
            'created_at' => $refund->created_at?->toIso8601String(),
            'lines' => $refund->lines
                ->map(fn (RefundLine $line): array => [
                    'sale_line_id' => $line->sale_line_id,
                    'quantity' => $line->quantity,
                    'amount' => Money::toDecimalString((int) $line->amount),
                    'restocked' => $line->restocked,
                ])
                ->values()
                ->all(),
        ];
    }
}
