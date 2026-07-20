<?php

declare(strict_types=1);

namespace App\Domain\Payments\DTOs;

use App\Domain\Payments\ValueObjects\PaymentWebhookEventType;

final readonly class NormalizedPaymentWebhook
{
    /**
     * @param  array<string, mixed>  $rawPayload
     */
    public function __construct(
        public string $provider,
        public string $providerEventId,
        public PaymentWebhookEventType $type,
        public string $transactionReference,
        public ?int $amountCents,
        public array $rawPayload,
    ) {}
}
