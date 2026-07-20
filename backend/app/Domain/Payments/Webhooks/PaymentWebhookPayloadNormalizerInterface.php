<?php

declare(strict_types=1);

namespace App\Domain\Payments\Webhooks;

use App\Domain\Payments\DTOs\NormalizedPaymentWebhook;

interface PaymentWebhookPayloadNormalizerInterface
{
    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws \App\Domain\Payments\Exceptions\PaymentDomainException
     */
    public function normalize(string $provider, array $payload): NormalizedPaymentWebhook;
}
