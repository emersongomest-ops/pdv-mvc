<?php

declare(strict_types=1);

namespace App\Application\CashShift\Actions;

use App\Application\Store\Support\AssertManagerStoreAccess;
use App\Domain\CashShift\Repositories\CashShiftRepositoryInterface;
use App\Models\CashShift;
use App\Models\User;
use Illuminate\Support\Collection;

final class ListAdminShiftsAction
{
    public function __construct(
        private readonly CashShiftRepositoryInterface $shifts,
        private readonly AssertManagerStoreAccess $storeAccess,
    ) {}

    /**
     * @return Collection<int, CashShift>
     */
    public function execute(User $manager, int $storeId): Collection
    {
        $this->storeAccess->assertCanAccess($manager, $storeId);

        return $this->shifts->listForStore($storeId);
    }
}
