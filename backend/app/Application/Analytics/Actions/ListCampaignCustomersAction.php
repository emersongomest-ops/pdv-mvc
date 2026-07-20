<?php

declare(strict_types=1);

namespace App\Application\Analytics\Actions;

use App\Domain\Analytics\Repositories\AnalyticsRepositoryInterface;
use App\Models\Customer;
use Illuminate\Support\Collection;

final class ListCampaignCustomersAction
{
    public function __construct(
        private readonly AnalyticsRepositoryInterface $analytics,
    ) {}

    /**
     * @return Collection<int, Customer>
     */
    public function execute(?int $birthMonth = null, ?string $region = null): Collection
    {
        return $this->analytics->listCampaignCustomers($birthMonth, $region);
    }
}
