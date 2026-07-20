<?php

declare(strict_types=1);

namespace App\Application\Payments\Actions;

use App\Domain\Payments\Exceptions\PaymentDomainException;
use App\Domain\Payments\Outbox\PendingPaymentOutboxInterface;
use App\Domain\Payments\Repositories\PaymentsRepositoryInterface;
use App\Domain\Payments\ValueObjects\PaymentLineStatus;
use App\Domain\Payments\ValueObjects\PaymentWebhookEventType;
use App\Domain\Payments\Webhooks\PaymentWebhookPayloadNormalizerInterface;
use App\Domain\Payments\Webhooks\PaymentWebhookSignatureVerifierInterface;
use App\Domain\Shared\ErrorCode;
use Illuminate\Support\Facades\DB;

final class ConsumePaymentWebhookAction
{
    public function __construct(
        private readonly PaymentWebhookSignatureVerifierInterface $signatures,
        private readonly PaymentWebhookPayloadNormalizerInterface $normalizer,
        private readonly PaymentsRepositoryInterface $payments,
        private readonly PendingPaymentOutboxInterface $outbox,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array{
     *     duplicate: bool,
     *     event_type: string,
     *     transaction_reference: string,
     *     payment_line_id: int|null,
     *     payment_status: string|null
     * }
     */
    public function execute(string $provider, string $rawBody, ?string $signatureHeader, array $payload): array
    {
        $this->signatures->assertValid($rawBody, $signatureHeader);

        $normalized = $this->normalizer->normalize($provider, $payload);

        return DB::transaction(function () use ($normalized): array {
            $line = $this->payments->findLineByTransactionReference($normalized->transactionReference);

            if ($line === null) {
                throw new PaymentDomainException(ErrorCode::PayWebhookUnknownReference);
            }

            $recorded = $this->payments->recordWebhookEvent($normalized);
            $event = $recorded['event'];

            if ($recorded['was_duplicate']) {
                if ($event->processing_status === 'processed') {
                    return [
                        'duplicate' => true,
                        'event_type' => $normalized->type->value,
                        'transaction_reference' => $normalized->transactionReference,
                        'payment_line_id' => null,
                        'payment_status' => null,
                    ];
                }
                // Duplicate delivery while still unprocessed — continue applying.
            }

            if (
                $normalized->amountCents !== null
                && (int) $line->amount !== $normalized->amountCents
            ) {
                throw new PaymentDomainException(ErrorCode::PayWebhookAmountMismatch);
            }

            $newStatus = match ($normalized->type) {
                PaymentWebhookEventType::PaymentConfirmed => PaymentLineStatus::Confirmed,
                PaymentWebhookEventType::PaymentFailed => PaymentLineStatus::Failed,
            };

            if ($line->status === PaymentLineStatus::Confirmed && $newStatus === PaymentLineStatus::Confirmed) {
                $this->payments->markWebhookProcessed($event);
                $this->outbox->forget($normalized->transactionReference);

                return [
                    'duplicate' => false,
                    'event_type' => $normalized->type->value,
                    'transaction_reference' => $normalized->transactionReference,
                    'payment_line_id' => $line->id,
                    'payment_status' => $line->status->value,
                ];
            }

            if ($line->status === PaymentLineStatus::Failed && $newStatus === PaymentLineStatus::Failed) {
                $this->payments->markWebhookProcessed($event);
                $this->outbox->forget($normalized->transactionReference);

                return [
                    'duplicate' => false,
                    'event_type' => $normalized->type->value,
                    'transaction_reference' => $normalized->transactionReference,
                    'payment_line_id' => $line->id,
                    'payment_status' => $line->status->value,
                ];
            }

            if ($line->status === PaymentLineStatus::Confirmed && $newStatus === PaymentLineStatus::Failed) {
                throw new PaymentDomainException(ErrorCode::PayWebhookInvalidTransition);
            }

            $updated = $this->payments->markLineStatus(
                $line,
                $newStatus,
                $newStatus === PaymentLineStatus::Confirmed ? now() : null,
            );

            $this->payments->markWebhookProcessed($event);
            $this->outbox->forget($normalized->transactionReference);

            return [
                'duplicate' => false,
                'event_type' => $normalized->type->value,
                'transaction_reference' => $normalized->transactionReference,
                'payment_line_id' => $updated->id,
                'payment_status' => $updated->status->value,
            ];
        });
    }
}
