<?php

declare(strict_types=1);

namespace App\Application\Sales\Actions;

use App\Application\Store\Support\AssertManagerStoreAccess;
use App\Domain\Sales\DTOs\AdminSaleFilters;
use App\Domain\Sales\Repositories\SalesRepositoryInterface;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Collection;

final class ListAdminSalesAction
{
    public function __construct(
        private readonly SalesRepositoryInterface $sales,
        private readonly AssertManagerStoreAccess $storeAccess,
    ) {}

    /**
     * @return Collection<int, Sale>
     */
    public function execute(User $manager, AdminSaleFilters $filters): Collection
    {
        $allowedStoreIds = $this->storeAccess->assignedStoreIds($manager);

        if ($filters->storeId !== null) {
            $this->storeAccess->assertCanAccess($manager, $filters->storeId);
        }

        if ($allowedStoreIds === []) {
            return collect();
        }

        return $this->sales->listForAdmin($filters, $allowedStoreIds);
    }
}
