<?php

declare(strict_types=1);

namespace App\Domain\Payments\Webhooks;

interface PaymentWebhookSignatureVerifierInterface
{
    /**
     * @throws \App\Domain\Payments\Exceptions\PaymentDomainException
     */
    public function assertValid(string $rawBody, ?string $signatureHeader): void;
}
