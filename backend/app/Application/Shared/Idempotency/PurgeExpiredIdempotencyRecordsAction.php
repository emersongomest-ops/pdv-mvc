<?php

declare(strict_types=1);

namespace App\Application\Shared\Idempotency;

use App\Domain\Shared\Idempotency\IdempotencyRecordRepositoryInterface;
use Carbon\CarbonImmutable;

/**
 * Deletes idempotency rows older than retention (RN-073: default 7 days).
 */
final class PurgeExpiredIdempotencyRecordsAction
{
    public function __construct(
        private readonly IdempotencyRecordRepositoryInterface $records,
    ) {}

    /**
     * @return array{deleted: int, retention_days: int, cutoff: string}
     */
    public function execute(?int $retentionDays = null): array
    {
        $days = $retentionDays ?? (int) config('idempotency.retention_days', 7);

        if ($days < 1) {
            $days = 7;
        }

        $cutoff = CarbonImmutable::now()->subDays($days);
        $deleted = $this->records->deleteCreatedBefore($cutoff);

        return [
            'deleted' => $deleted,
            'retention_days' => $days,
            'cutoff' => $cutoff->toIso8601String(),
        ];
    }
}
