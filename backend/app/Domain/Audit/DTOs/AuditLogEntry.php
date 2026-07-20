<?php

declare(strict_types=1);

namespace App\Domain\Audit\DTOs;

use App\Domain\Audit\ValueObjects\AuditAction;

final readonly class AuditLogEntry
{
    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     * @param  array<string, mixed>|null  $metadata
     */
    public function __construct(
        public AuditAction $action,
        public int $actorUserId,
        public string $subjectType,
        public int $subjectId,
        public ?int $storeId = null,
        public ?array $oldValues = null,
        public ?array $newValues = null,
        public ?array $metadata = null,
    ) {}
}
