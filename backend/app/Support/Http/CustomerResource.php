<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Domain\Shared\Money;
use App\Models\Customer;
use App\Models\CustomerStoreStat;
use App\Support\Pii\PiiCrypto;

final class CustomerResource
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(Customer $customer): array
    {
        return self::serialize($customer, maskCpf: false);
    }

    /**
     * Operational POS lookup — minimize PII exposure (LGPD).
     *
     * @return array<string, mixed>
     */
    public static function toOperationalArray(Customer $customer): array
    {
        return self::serialize($customer, maskCpf: true);
    }

    /**
     * @return array<string, mixed>
     */
    private static function serialize(Customer $customer, bool $maskCpf): array
    {
        $customer->loadMissing('storeStats');

        return [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'cpf' => $maskCpf ? PiiCrypto::maskCpf((string) $customer->cpf) : $customer->cpf,
            'phone' => $customer->phone,
            'birth_date' => $customer->birth_date?->format('Y-m-d'),
            'address' => $customer->address,
            'lifetime_spend' => Money::toDecimalString((int) $customer->lifetime_spend),
            'store_stats' => $customer->storeStats
                ->map(fn (CustomerStoreStat $stat): array => [
                    'store_id' => $stat->store_id,
                    'purchase_count' => $stat->purchase_count,
                    'total_spend' => Money::toDecimalString((int) $stat->total_spend),
                ])
                ->values()
                ->all(),
        ];
    }
}
