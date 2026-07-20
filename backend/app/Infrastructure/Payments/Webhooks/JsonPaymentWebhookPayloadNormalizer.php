<?php

declare(strict_types=1);

namespace App\Infrastructure\Payments\Webhooks;

use App\Domain\Payments\DTOs\NormalizedPaymentWebhook;
use App\Domain\Payments\Exceptions\PaymentDomainException;
use App\Domain\Payments\ValueObjects\PaymentWebhookEventType;
use App\Domain\Payments\Webhooks\PaymentWebhookPayloadNormalizerInterface;
use App\Domain\Shared\ErrorCode;
use App\Domain\Shared\Money;

final class JsonPaymentWebhookPayloadNormalizer implements PaymentWebhookPayloadNormalizerInterface
{
    private const ALLOWED_PROVIDERS = ['stub', 'mercadopago', 'stripe'];

    public function normalize(string $provider, array $payload): NormalizedPaymentWebhook
    {
        $provider = strtolower(trim($provider));

        if (! in_array($provider, self::ALLOWED_PROVIDERS, true)) {
            throw new PaymentDomainException(ErrorCode::PayWebhookProviderUnsupported);
        }

        $eventId = $payload['event_id'] ?? $payload['id'] ?? null;
        $typeRaw = $payload['type'] ?? $payload['event_type'] ?? null;
        $reference = $payload['transaction_reference']
            ?? $payload['external_reference']
            ?? $payload['data']['transaction_reference']
            ?? null;

        if (! is_string($eventId) || $eventId === '') {
            throw new PaymentDomainException(ErrorCode::PayWebhookPayloadInvalid);
        }

        if (! is_string($typeRaw) || $typeRaw === '') {
            throw new PaymentDomainException(ErrorCode::PayWebhookPayloadInvalid);
        }

        if (! is_string($reference) || $reference === '') {
            throw new PaymentDomainException(ErrorCode::PayWebhookPayloadInvalid);
        }

        $type = PaymentWebhookEventType::tryFrom($typeRaw);

        if ($type === null) {
            throw new PaymentDomainException(ErrorCode::PayWebhookPayloadInvalid);
        }

        $amountCents = null;
        if (array_key_exists('amount', $payload) && $payload['amount'] !== null) {
            $amountCents = is_int($payload['amount'])
                ? $payload['amount']
                : Money::fromDecimalInput($payload['amount']);
        }

        return new NormalizedPaymentWebhook(
            provider: $provider,
            providerEventId: $eventId,
            type: $type,
            transactionReference: $reference,
            amountCents: $amountCents,
            rawPayload: $payload,
        );
    }
}
