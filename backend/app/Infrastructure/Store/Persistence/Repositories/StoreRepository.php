<?php

declare(strict_types=1);

namespace App\Infrastructure\Store\Persistence\Repositories;

use App\Domain\Store\Repositories\StoreRepositoryInterface;
use App\Models\Store;
use App\Models\User;

final class StoreRepository implements StoreRepositoryInterface
{
    public function findById(int $id): ?Store
    {
        return Store::query()->find($id);
    }

    /**
     * @return list<Store>
     */
    public function listAccessibleForUser(User $user): array
    {
        return $user->stores()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->all();
    }

    public function userCanAccessStore(User $user, int $storeId): bool
    {
        return $user->stores()->where('stores.id', $storeId)->exists();
    }

    /**
     * @return list<int>
     */
    public function assignedStoreIds(User $user): array
    {
        return $user->stores()
            ->orderBy('stores.id')
            ->pluck('stores.id')
            ->map(static fn ($id): int => (int) $id)
            ->values()
            ->all();
    }
}
