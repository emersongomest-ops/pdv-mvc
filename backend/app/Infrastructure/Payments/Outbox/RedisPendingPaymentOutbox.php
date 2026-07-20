<?php

declare(strict_types=1);

namespace App\Infrastructure\Payments\Outbox;

use App\Domain\Payments\DTOs\PendingPaymentOutboxEntry;
use App\Domain\Payments\Outbox\PendingPaymentOutboxInterface;
use Illuminate\Support\Facades\Redis;

final class RedisPendingPaymentOutbox implements PendingPaymentOutboxInterface
{
    private const INDEX_KEY = 'payments:outbox:index';

    private function entryKey(string $transactionReference): string
    {
        return 'payments:outbox:entry:'.$transactionReference;
    }

    public function push(PendingPaymentOutboxEntry $entry): void
    {
        Redis::set(
            $this->entryKey($entry->transactionReference),
            json_encode($entry->toArray(), JSON_THROW_ON_ERROR),
        );
        Redis::sadd(self::INDEX_KEY, $entry->transactionReference);
    }

    public function forget(string $transactionReference): void
    {
        Redis::del($this->entryKey($transactionReference));
        Redis::srem(self::INDEX_KEY, $transactionReference);
    }

    public function all(): array
    {
        /** @var list<string> $refs */
        $refs = Redis::smembers(self::INDEX_KEY) ?: [];
        $entries = [];

        foreach ($refs as $ref) {
            $raw = Redis::get($this->entryKey($ref));
            if (! is_string($raw) || $raw === '') {
                Redis::srem(self::INDEX_KEY, $ref);
                continue;
            }

            /** @var array<string, mixed> $decoded */
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            $entries[] = PendingPaymentOutboxEntry::fromArray($decoded);
        }

        return $entries;
    }

    public function incrementAttempts(string $transactionReference): void
    {
        $raw = Redis::get($this->entryKey($transactionReference));
        if (! is_string($raw) || $raw === '') {
            return;
        }

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        $entry = PendingPaymentOutboxEntry::fromArray($decoded);
        $this->push(new PendingPaymentOutboxEntry(
            transactionReference: $entry->transactionReference,
            paymentLineId: $entry->paymentLineId,
            saleId: $entry->saleId,
            storeId: $entry->storeId,
            provider: $entry->provider,
            attempts: $entry->attempts + 1,
            enqueuedAt: $entry->enqueuedAt,
        ));
    }
}
