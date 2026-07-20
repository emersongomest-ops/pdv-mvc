<?php

declare(strict_types=1);

namespace App\Application\Store\Actions;

use App\Domain\Store\Repositories\StoreRepositoryInterface;
use App\Models\Store;
use App\Models\User;

final class ListAccessibleStoresAction
{
    public function __construct(
        private readonly StoreRepositoryInterface $stores,
    ) {}

    /**
     * @return list<Store>
     */
    public function execute(User $user): array
    {
        return $this->stores->listAccessibleForUser($user);
    }
}
