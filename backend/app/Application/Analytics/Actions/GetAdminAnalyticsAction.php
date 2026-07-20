<?php

declare(strict_types=1);

namespace App\Application\Analytics\Actions;

use App\Application\Store\Support\AssertManagerStoreAccess;
use App\Domain\Analytics\DTOs\AdminAnalyticsSnapshot;
use App\Domain\Analytics\Repositories\AnalyticsRepositoryInterface;
use App\Models\User;

final class GetAdminAnalyticsAction
{
    public function __construct(
        private readonly AnalyticsRepositoryInterface $analytics,
        private readonly AssertManagerStoreAccess $storeAccess,
    ) {}

    public function execute(User $manager, int $registrationDays = 30, int $topCustomers = 20): AdminAnalyticsSnapshot
    {
        $storeIds = $this->storeAccess->assignedStoreIds($manager);

        return $this->analytics->adminSnapshot($storeIds, $registrationDays, $topCustomers);
    }
}
