<?php

declare(strict_types=1);

namespace App\Domain\Store\Repositories;

use App\Models\Store;
use App\Models\User;

interface StoreRepositoryInterface
{
    public function findById(int $id): ?Store;

    /**
     * @return list<Store>
     */
    public function listAccessibleForUser(User $user): array;

    public function userCanAccessStore(User $user, int $storeId): bool;

    /**
     * Store IDs assigned via store_user (includes inactive stores for history reads).
     *
     * @return list<int>
     */
    public function assignedStoreIds(User $user): array;
}
