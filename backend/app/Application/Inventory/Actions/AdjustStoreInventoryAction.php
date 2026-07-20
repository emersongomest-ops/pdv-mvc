<?php

declare(strict_types=1);

namespace App\Application\Inventory\Actions;

use App\Application\Store\Support\AssertManagerStoreAccess;
use App\Domain\Audit\DTOs\AuditLogEntry;
use App\Domain\Audit\Repositories\AuditLogRepositoryInterface;
use App\Domain\Audit\ValueObjects\AuditAction;
use App\Domain\Catalog\Exceptions\CatalogDomainException;
use App\Domain\Catalog\Repositories\CatalogRepositoryInterface;
use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\StoreInventory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class AdjustStoreInventoryAction
{
    public function __construct(
        private readonly InventoryRepositoryInterface $inventory,
        private readonly CatalogRepositoryInterface $catalog,
        private readonly AssertManagerStoreAccess $storeAccess,
        private readonly AuditLogRepositoryInterface $auditLogs,
    ) {}

    public function execute(
        User $manager,
        int $storeId,
        int $productId,
        int $quantity,
        string $reason,
    ): StoreInventory {
        $this->storeAccess->assertCanAccess($manager, $storeId);

        if ($this->catalog->findProductById($productId) === null) {
            throw new CatalogDomainException(ErrorCode::CatProductNotFound);
        }

        return DB::transaction(function () use ($manager, $storeId, $productId, $quantity, $reason): StoreInventory {
            $existing = $this->inventory->findForStoreProduct($storeId, $productId);
            $previousQuantity = $existing?->quantity ?? 0;

            $inventory = $this->inventory->adjustQuantity(
                $storeId,
                $productId,
                $quantity,
                $reason,
                (int) $manager->id,
            );

            $this->auditLogs->append(new AuditLogEntry(
                action: AuditAction::StockAdjusted,
                actorUserId: (int) $manager->id,
                subjectType: 'product',
                subjectId: $productId,
                storeId: $storeId,
                oldValues: ['quantity' => $previousQuantity],
                newValues: ['quantity' => (int) $inventory->quantity],
                metadata: [
                    'reason' => $reason,
                    'store_inventory_id' => $inventory->id,
                ],
            ));

            return $inventory;
        });
    }
}
