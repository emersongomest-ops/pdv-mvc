<?php

declare(strict_types=1);

namespace App\Domain\Shared\Idempotency;

use App\Models\IdempotencyRecord;

interface IdempotencyRecordRepositoryInterface
{
    public function findByScopeAndKey(string $scope, string $key): ?IdempotencyRecord;

    public function claimProcessing(
        string $scope,
        string $key,
        int $userId,
        string $requestHash,
        ?string $requestId,
    ): IdempotencyRecord;

    /**
     * @param  array<string, mixed>  $responseBody
     */
    public function markCompleted(IdempotencyRecord $record, int $responseCode, array $responseBody): void;

    public function delete(IdempotencyRecord $record): void;

    /**
     * @return int Number of deleted rows
     */
    public function deleteCreatedBefore(\DateTimeInterface $cutoff): int;
}
