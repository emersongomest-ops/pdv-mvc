<?php

declare(strict_types=1);

namespace App\Domain\Payments\Outbox;

use App\Domain\Payments\DTOs\PendingPaymentOutboxEntry;

interface PendingPaymentOutboxInterface
{
    public function push(PendingPaymentOutboxEntry $entry): void;

    public function forget(string $transactionReference): void;

    /**
     * @return list<PendingPaymentOutboxEntry>
     */
    public function all(): array;

    public function incrementAttempts(string $transactionReference): void;
}
