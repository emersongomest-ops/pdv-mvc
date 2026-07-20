<?php

declare(strict_types=1);

namespace App\Domain\Analytics\Repositories;

use App\Domain\Analytics\DTOs\AdminAnalyticsSnapshot;
use App\Models\Customer;
use Illuminate\Support\Collection;

interface AnalyticsRepositoryInterface
{
    /**
     * @param  list<int>  $storeIds
     */
    public function adminSnapshot(array $storeIds, int $registrationDays = 30, int $topCustomers = 20): AdminAnalyticsSnapshot;

    /**
     * Campaign filters (RN-083 / RN-084).
     *
     * @return Collection<int, Customer>
     */
    public function listCampaignCustomers(?int $birthMonth = null, ?string $region = null): Collection;
}
