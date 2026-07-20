<?php

declare(strict_types=1);

namespace App\Domain\Analytics\DTOs;

/**
 * @param  list<RegistrationBucket>  $registrationsOverTime
 * @param  list<CustomerSpendRow>  $topCustomersBySpend
 */
final readonly class AdminAnalyticsSnapshot
{
    /**
     * @param  list<RegistrationBucket>  $registrationsOverTime
     * @param  list<CustomerSpendRow>  $topCustomersBySpend
     */
    public function __construct(
        public array $registrationsOverTime,
        public int $customersWithPurchases,
        public int $customersWithRepeatPurchases,
        public float $recurrenceIndex,
        public array $topCustomersBySpend,
    ) {}
}
