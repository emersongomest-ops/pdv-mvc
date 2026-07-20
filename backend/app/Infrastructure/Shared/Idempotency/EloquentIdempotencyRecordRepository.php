<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Idempotency;

use App\Domain\Shared\Idempotency\IdempotencyRecordRepositoryInterface;
use App\Models\IdempotencyRecord;

final class EloquentIdempotencyRecordRepository implements IdempotencyRecordRepositoryInterface
{
    public function findByScopeAndKey(string $scope, string $key): ?IdempotencyRecord
    {
        return IdempotencyRecord::query()
            ->where('scope', $scope)
            ->where('key', $key)
            ->first();
    }

    public function claimProcessing(
        string $scope,
        string $key,
        int $userId,
        string $requestHash,
        ?string $requestId,
    ): IdempotencyRecord {
        return IdempotencyRecord::query()->create([
            'key' => $key,
            'scope' => $scope,
            'user_id' => $userId,
            'request_hash' => $requestHash,
            'request_id' => $requestId,
            'status' => IdempotencyRecord::STATUS_PROCESSING,
        ]);
    }

    public function markCompleted(IdempotencyRecord $record, int $responseCode, array $responseBody): void
    {
        $record->forceFill([
            'status' => IdempotencyRecord::STATUS_COMPLETED,
            'response_code' => $responseCode,
            'response_body' => $responseBody,
        ])->save();
    }

    public function delete(IdempotencyRecord $record): void
    {
        $record->delete();
    }

    public function deleteCreatedBefore(\DateTimeInterface $cutoff): int
    {
        return IdempotencyRecord::query()
            ->where('created_at', '<', $cutoff)
            ->delete();
    }
}
