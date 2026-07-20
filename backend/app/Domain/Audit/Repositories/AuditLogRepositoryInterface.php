<?php

declare(strict_types=1);

namespace App\Domain\Audit\Repositories;

use App\Domain\Audit\DTOs\AdminAuditFilters;
use App\Domain\Audit\DTOs\AuditLogEntry;
use App\Models\AuditLog;
use Illuminate\Support\Collection;

interface AuditLogRepositoryInterface
{
    public function append(AuditLogEntry $entry): AuditLog;

    /**
     * @param  list<int>  $allowedStoreIds
     * @return array{items: Collection<int, AuditLog>, next_cursor: string|null}
     */
    public function listForAdmin(AdminAuditFilters $filters, array $allowedStoreIds): array;
}
