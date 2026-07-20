<?php

declare(strict_types=1);

namespace App\Infrastructure\Payments\Webhooks;

use App\Domain\Payments\Exceptions\PaymentDomainException;
use App\Domain\Payments\Webhooks\PaymentWebhookSignatureVerifierInterface;
use App\Domain\Shared\ErrorCode;

final class HmacPaymentWebhookSignatureVerifier implements PaymentWebhookSignatureVerifierInterface
{
    public function assertValid(string $rawBody, ?string $signatureHeader): void
    {
        $secret = (string) config('payments.webhook.secret', '');

        if ($secret === '') {
            throw new PaymentDomainException(ErrorCode::PayWebhookInvalidSignature);
        }

        if ($signatureHeader === null || $signatureHeader === '') {
            throw new PaymentDomainException(ErrorCode::PayWebhookInvalidSignature);
        }

        $provided = $signatureHeader;

        if (str_starts_with(strtolower($provided), 'sha256=')) {
            $provided = substr($provided, 7);
        }

        $expected = hash_hmac('sha256', $rawBody, $secret);

        if (! hash_equals($expected, $provided)) {
            throw new PaymentDomainException(ErrorCode::PayWebhookInvalidSignature);
        }
    }
}
