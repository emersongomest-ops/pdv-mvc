<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Domain\Shared\Money;
use App\Models\AuditLog;

final class AuditLogResource
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(AuditLog $log): array
    {
        $log->loadMissing(['actor', 'store']);

        return [
            'id' => $log->id,
            'action' => $log->action->value,
            'actor' => $log->actor === null ? null : [
                'id' => $log->actor->id,
                'name' => $log->actor->name,
                'email' => $log->actor->email,
            ],
            'store' => $log->store === null ? null : [
                'id' => $log->store->id,
                'name' => $log->store->name,
                'code' => $log->store->code,
            ],
            'subject_type' => $log->subject_type,
            'subject_id' => $log->subject_id,
            'old_values' => self::formatValues($log->old_values),
            'new_values' => self::formatValues($log->new_values),
            'metadata' => self::formatValues($log->metadata),
            'occurred_at' => $log->occurred_at?->toIso8601String(),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $values
     * @return array<string, mixed>|null
     */
    private static function formatValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $formatted = $values;

        foreach (['base_price', 'amount', 'discount_value'] as $moneyKey) {
            if (array_key_exists($moneyKey, $formatted) && is_int($formatted[$moneyKey])) {
                $formatted[$moneyKey] = Money::toDecimalString($formatted[$moneyKey]);
            }
        }

        if (isset($formatted['lines']) && is_array($formatted['lines'])) {
            $formatted['lines'] = array_map(static function (mixed $line): mixed {
                if (! is_array($line)) {
                    return $line;
                }
                if (isset($line['amount']) && is_int($line['amount'])) {
                    $line['amount'] = Money::toDecimalString($line['amount']);
                }

                return $line;
            }, $formatted['lines']);
        }

        return $formatted;
    }
}
