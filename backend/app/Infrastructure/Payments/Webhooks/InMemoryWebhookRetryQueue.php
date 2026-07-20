<?php

declare(strict_types=1);

namespace App\Infrastructure\Payments\Webhooks;

use App\Domain\Payments\DTOs\WebhookRetryItem;
use App\Domain\Payments\Webhooks\WebhookRetryQueueInterface;

final class InMemoryWebhookRetryQueue implements WebhookRetryQueueInterface
{
    /** @var list<WebhookRetryItem> */
    private array $items = [];

    public function push(WebhookRetryItem $item): void
    {
        $this->items[] = $item;
    }

    public function popMany(int $limit): array
    {
        $limit = max(0, $limit);
        $chunk = array_splice($this->items, 0, $limit);

        return array_values($chunk);
    }

    public function size(): int
    {
        return count($this->items);
    }

    public function clear(): void
    {
        $this->items = [];
    }
}
