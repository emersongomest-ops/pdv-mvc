<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Domain\Analytics\DTOs\AdminAnalyticsSnapshot;
use App\Domain\Analytics\DTOs\CustomerSpendRow;
use App\Domain\Analytics\DTOs\RegistrationBucket;
use App\Domain\Shared\Money;

final class AnalyticsResource
{
    /**
     * @return array<string, mixed>
     */
    public static function snapshotToArray(AdminAnalyticsSnapshot $snapshot): array
    {
        return [
            'registrations_over_time' => array_map(
                static fn (RegistrationBucket $bucket): array => [
                    'date' => $bucket->date,
                    'count' => $bucket->count,
                ],
                $snapshot->registrationsOverTime,
            ),
            'recurrence' => [
                'customers_with_purchases' => $snapshot->customersWithPurchases,
                'customers_with_repeat' => $snapshot->customersWithRepeatPurchases,
                'index' => number_format($snapshot->recurrenceIndex, 4, '.', ''),
            ],
            'top_customers_by_spend' => array_map(
                static fn (CustomerSpendRow $row): array => self::customerSpendToArray($row),
                $snapshot->topCustomersBySpend,
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function customerSpendToArray(CustomerSpendRow $row): array
    {
        return [
            'customer_id' => $row->customerId,
            'name' => $row->name,
            'cpf' => $row->cpf,
            'lifetime_spend' => Money::toDecimalString($row->lifetimeSpendCents),
            'store_spend' => array_map(
                static fn (array $item): array => [
                    'store_id' => $item['store_id'],
                    'store_code' => $item['store_code'],
                    'purchase_count' => $item['purchase_count'],
                    'total_spend' => Money::toDecimalString($item['total_spend_cents']),
                ],
                $row->storeSpend,
            ),
        ];
    }
}
