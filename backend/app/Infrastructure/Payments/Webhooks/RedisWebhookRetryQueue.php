<?php

declare(strict_types=1);

namespace App\Infrastructure\Payments\Webhooks;

use App\Domain\Payments\DTOs\WebhookRetryItem;
use App\Domain\Payments\Webhooks\WebhookRetryQueueInterface;
use Illuminate\Support\Facades\Redis;

final class RedisWebhookRetryQueue implements WebhookRetryQueueInterface
{
    private const LIST_KEY = 'payments:webhook-retry';

    public function push(WebhookRetryItem $item): void
    {
        Redis::rpush(self::LIST_KEY, json_encode($item->toArray(), JSON_THROW_ON_ERROR));
    }

    public function popMany(int $limit): array
    {
        $items = [];
        for ($i = 0; $i < $limit; $i++) {
            $raw = Redis::lpop(self::LIST_KEY);
            if (! is_string($raw) || $raw === '') {
                break;
            }

            /** @var array<string, mixed> $decoded */
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            $items[] = WebhookRetryItem::fromArray($decoded);
        }

        return $items;
    }

    public function size(): int
    {
        return (int) Redis::llen(self::LIST_KEY);
    }
}
