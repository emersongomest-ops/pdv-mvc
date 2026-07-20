<?php

declare(strict_types=1);

namespace App\Domain\Payments\Webhooks;

use App\Domain\Payments\DTOs\WebhookRetryItem;

interface WebhookRetryQueueInterface
{
    public function push(WebhookRetryItem $item): void;

    /**
     * Pop up to $limit items (FIFO). Failed re-push is caller's responsibility.
     *
     * @return list<WebhookRetryItem>
     */
    public function popMany(int $limit): array;

    public function size(): int;
}
