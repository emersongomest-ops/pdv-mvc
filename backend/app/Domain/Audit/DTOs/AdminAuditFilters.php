<?php

declare(strict_types=1);

namespace App\Domain\Audit\DTOs;

final readonly class AdminAuditFilters
{
    public function __construct(
        public ?string $fromDate = null,
        public ?string $toDate = null,
        public ?string $action = null,
        public ?int $actorUserId = null,
        public ?int $storeId = null,
        public ?string $subjectType = null,
        public ?int $subjectId = null,
        public ?string $cursor = null,
        public int $perPage = 50,
    ) {}
}
