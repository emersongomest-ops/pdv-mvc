<?php

declare(strict_types=1);

namespace App\Application\Audit\Actions;

use App\Application\Store\Support\AssertManagerStoreAccess;
use App\Domain\Audit\DTOs\AdminAuditFilters;
use App\Domain\Audit\Repositories\AuditLogRepositoryInterface;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Collection;

final class ListAdminAuditLogsAction
{
    public function __construct(
        private readonly AuditLogRepositoryInterface $auditLogs,
        private readonly AssertManagerStoreAccess $storeAccess,
    ) {}

    /**
     * @return array{items: Collection<int, AuditLog>, next_cursor: string|null}
     */
    public function execute(User $manager, AdminAuditFilters $filters): array
    {
        $allowedStoreIds = $this->storeAccess->assignedStoreIds($manager);

        if ($filters->storeId !== null) {
            $this->storeAccess->assertCanAccess($manager, $filters->storeId);
        }

        return $this->auditLogs->listForAdmin($filters, $allowedStoreIds);
    }
}
