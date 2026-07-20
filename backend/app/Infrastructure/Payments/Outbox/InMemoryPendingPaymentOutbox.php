<?php

declare(strict_types=1);

namespace App\Infrastructure\Payments\Outbox;

use App\Domain\Payments\DTOs\PendingPaymentOutboxEntry;
use App\Domain\Payments\Outbox\PendingPaymentOutboxInterface;

/** Process-local outbox for tests / when Redis driver is not used. */
final class InMemoryPendingPaymentOutbox implements PendingPaymentOutboxInterface
{
    /** @var array<string, PendingPaymentOutboxEntry> */
    private array $entries = [];

    public function push(PendingPaymentOutboxEntry $entry): void
    {
        $this->entries[$entry->transactionReference] = $entry;
    }

    public function forget(string $transactionReference): void
    {
        unset($this->entries[$transactionReference]);
    }

    public function all(): array
    {
        return array_values($this->entries);
    }

    public function incrementAttempts(string $transactionReference): void
    {
        $entry = $this->entries[$transactionReference] ?? null;
        if ($entry === null) {
            return;
        }

        $this->entries[$transactionReference] = new PendingPaymentOutboxEntry(
            transactionReference: $entry->transactionReference,
            paymentLineId: $entry->paymentLineId,
            saleId: $entry->saleId,
            storeId: $entry->storeId,
            provider: $entry->provider,
            attempts: $entry->attempts + 1,
            enqueuedAt: $entry->enqueuedAt,
        );
    }

    public function clear(): void
    {
        $this->entries = [];
    }
}
