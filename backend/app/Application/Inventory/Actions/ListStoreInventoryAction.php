<?php

declare(strict_types=1);

namespace App\Application\Inventory\Actions;

use App\Application\Store\Support\AssertManagerStoreAccess;
use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;
use App\Models\StoreInventory;
use App\Models\User;
use Illuminate\Support\Collection;

final class ListStoreInventoryAction
{
    public function __construct(
        private readonly InventoryRepositoryInterface $inventory,
        private readonly AssertManagerStoreAccess $storeAccess,
    ) {}

    /**
     * @return Collection<int, StoreInventory>
     */
    public function execute(User $manager, int $storeId): Collection
    {
        $this->storeAccess->assertCanAccess($manager, $storeId);

        return $this->inventory->listForStore($storeId);
    }
}
