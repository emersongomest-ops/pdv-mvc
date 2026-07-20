<?php

declare(strict_types=1);

namespace App\Infrastructure\Audit\Persistence\Repositories;

use App\Domain\Audit\DTOs\AdminAuditFilters;
use App\Domain\Audit\DTOs\AuditLogEntry;
use App\Domain\Audit\Repositories\AuditLogRepositoryInterface;
use App\Models\AuditLog;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class AuditLogRepository implements AuditLogRepositoryInterface
{
    public function append(AuditLogEntry $entry): AuditLog
    {
        return AuditLog::query()->create([
            'action' => $entry->action->value,
            'actor_user_id' => $entry->actorUserId,
            'store_id' => $entry->storeId,
            'subject_type' => $entry->subjectType,
            'subject_id' => $entry->subjectId,
            'old_values' => $entry->oldValues,
            'new_values' => $entry->newValues,
            'metadata' => $entry->metadata,
            'occurred_at' => now(),
        ]);
    }

    public function listForAdmin(AdminAuditFilters $filters, array $allowedStoreIds): array
    {
        $perPage = max(1, min(100, $filters->perPage));

        $query = AuditLog::query()
            ->with(['actor:id,name,email', 'store:id,name,code'])
            ->orderByDesc('occurred_at')
            ->orderByDesc('id');

        $query->where(function ($builder) use ($allowedStoreIds): void {
            $builder->whereNull('store_id');
            if ($allowedStoreIds !== []) {
                $builder->orWhereIn('store_id', $allowedStoreIds);
            }
        });

        if ($filters->storeId !== null) {
            $query->where('store_id', $filters->storeId);
        }

        if ($filters->action !== null) {
            $query->where('action', $filters->action);
        }

        if ($filters->actorUserId !== null) {
            $query->where('actor_user_id', $filters->actorUserId);
        }

        if ($filters->subjectType !== null) {
            $query->where('subject_type', $filters->subjectType);
        }

        if ($filters->subjectId !== null) {
            $query->where('subject_id', $filters->subjectId);
        }

        if ($filters->fromDate !== null) {
            $query->whereDate('occurred_at', '>=', $filters->fromDate);
        }

        if ($filters->toDate !== null) {
            $query->whereDate('occurred_at', '<=', $filters->toDate);
        }

        if ($filters->cursor !== null && $filters->cursor !== '') {
            $cursorId = $this->decodeCursor($filters->cursor);
            $query->where('id', '<', $cursorId);
        }

        /** @var Collection<int, AuditLog> $rows */
        $rows = $query->limit($perPage + 1)->get();

        $nextCursor = null;
        if ($rows->count() > $perPage) {
            $rows = $rows->take($perPage)->values();
            $last = $rows->last();
            if ($last !== null) {
                $nextCursor = $this->encodeCursor($last);
            }
        }

        return [
            'items' => $rows->values(),
            'next_cursor' => $nextCursor,
        ];
    }

    private function encodeCursor(AuditLog $log): string
    {
        return rtrim(strtr(base64_encode((string) $log->id), '+/', '-_'), '=');
    }

    private function decodeCursor(string $cursor): int
    {
        $decoded = base64_decode(strtr($cursor, '-_', '+/'), true);
        if ($decoded === false || ! ctype_digit($decoded) || (int) $decoded < 1) {
            throw new InvalidArgumentException('Invalid audit cursor.');
        }

        return (int) $decoded;
    }
}
