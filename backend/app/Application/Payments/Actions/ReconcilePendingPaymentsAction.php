<?php

declare(strict_types=1);

namespace App\Application\Payments\Actions;

use App\Domain\Payments\DTOs\WebhookRetryItem;
use App\Domain\Payments\Exceptions\PaymentDomainException;
use App\Domain\Payments\Gateways\PaymentGatewayInterface;
use App\Domain\Payments\Outbox\PendingPaymentOutboxInterface;
use App\Domain\Payments\Repositories\PaymentsRepositoryInterface;
use App\Domain\Payments\ValueObjects\PaymentChargeStatus;
use App\Domain\Payments\ValueObjects\PaymentLineStatus;
use App\Domain\Payments\Webhooks\WebhookRetryQueueInterface;
use App\Domain\Shared\ErrorCode;
use Throwable;

/**
 * Option A: poll acquirer for pending payment lines / outbox.
 * Option B: re-drive failed inbound webhooks from the retry queue.
 */
final class ReconcilePendingPaymentsAction
{
    public function __construct(
        private readonly PendingPaymentOutboxInterface $outbox,
        private readonly WebhookRetryQueueInterface $webhookRetries,
        private readonly PaymentsRepositoryInterface $payments,
        private readonly PaymentGatewayInterface $gateway,
        private readonly ConsumePaymentWebhookAction $consumeWebhook,
    ) {}

    /**
     * @return array{
     *     webhook_retries_attempted: int,
     *     webhook_retries_succeeded: int,
     *     webhook_retries_requeued: int,
     *     settlements_attempted: int,
     *     settlements_confirmed: int,
     *     settlements_failed: int,
     *     still_pending: int
     * }
     */
    public function execute(?int $storeId = null): array
    {
        $webhook = $this->drainWebhookRetries();
        $settlements = $this->settlePendingCharges($storeId);

        return [
            ...$webhook,
            ...$settlements,
            'still_pending' => $this->payments->countPendingLines($storeId),
        ];
    }

    /**
     * @return array{
     *     webhook_retries_attempted: int,
     *     webhook_retries_succeeded: int,
     *     webhook_retries_requeued: int
     * }
     */
    private function drainWebhookRetries(): array
    {
        $limit = (int) config('payments.reconcile.webhook_batch', 50);
        $items = $this->webhookRetries->popMany($limit);
        $succeeded = 0;
        $requeued = 0;
        $maxAttempts = (int) config('payments.reconcile.webhook_max_attempts', 24);

        foreach ($items as $item) {
            try {
                $this->consumeWebhook->execute(
                    provider: $item->provider,
                    rawBody: $item->rawBody,
                    signatureHeader: $item->signatureHeader,
                    payload: $item->payload,
                );
                $succeeded++;
            } catch (PaymentDomainException $exception) {
                if ($this->shouldDropWebhookRetry($exception->errorCode)) {
                    continue;
                }

                if ($item->attempts + 1 >= $maxAttempts) {
                    continue;
                }

                $this->webhookRetries->push(new WebhookRetryItem(
                    provider: $item->provider,
                    rawBody: $item->rawBody,
                    signatureHeader: $item->signatureHeader,
                    payload: $item->payload,
                    attempts: $item->attempts + 1,
                    lastError: $exception->errorCode->value,
                    enqueuedAt: $item->enqueuedAt,
                ));
                $requeued++;
            } catch (Throwable $exception) {
                if ($item->attempts + 1 >= $maxAttempts) {
                    continue;
                }

                $this->webhookRetries->push(new WebhookRetryItem(
                    provider: $item->provider,
                    rawBody: $item->rawBody,
                    signatureHeader: $item->signatureHeader,
                    payload: $item->payload,
                    attempts: $item->attempts + 1,
                    lastError: $exception->getMessage(),
                    enqueuedAt: $item->enqueuedAt,
                ));
                $requeued++;
            }
        }

        return [
            'webhook_retries_attempted' => count($items),
            'webhook_retries_succeeded' => $succeeded,
            'webhook_retries_requeued' => $requeued,
        ];
    }

    /**
     * @return array{
     *     settlements_attempted: int,
     *     settlements_confirmed: int,
     *     settlements_failed: int
     * }
     */
    private function settlePendingCharges(?int $storeId): array
    {
        $attempted = 0;
        $confirmed = 0;
        $failed = 0;

        $refs = [];
        foreach ($this->outbox->all() as $entry) {
            if ($storeId !== null && $entry->storeId !== null && $entry->storeId !== $storeId) {
                continue;
            }
            $refs[$entry->transactionReference] = true;
        }

        foreach ($this->payments->listPendingLines($storeId) as $line) {
            $ref = (string) $line->transaction_reference;
            if ($ref !== '') {
                $refs[$ref] = true;
            }
        }

        foreach (array_keys($refs) as $transactionReference) {
            $attempted++;
            $this->outbox->incrementAttempts($transactionReference);

            try {
                $status = $this->gateway->queryChargeStatus($transactionReference);
            } catch (PaymentDomainException) {
                continue;
            }

            $line = $this->payments->findLineByTransactionReference($transactionReference);
            if ($line === null) {
                continue;
            }

            if ($line->status !== PaymentLineStatus::Pending) {
                $this->outbox->forget($transactionReference);
                continue;
            }

            if ($status === PaymentChargeStatus::Pending) {
                continue;
            }

            if ($status === PaymentChargeStatus::Confirmed) {
                $this->payments->markLineStatus($line, PaymentLineStatus::Confirmed, now());
                $this->outbox->forget($transactionReference);
                $confirmed++;
                continue;
            }

            $this->payments->markLineStatus($line, PaymentLineStatus::Failed, null);
            $this->outbox->forget($transactionReference);
            $failed++;
        }

        return [
            'settlements_attempted' => $attempted,
            'settlements_confirmed' => $confirmed,
            'settlements_failed' => $failed,
        ];
    }

    private function shouldDropWebhookRetry(ErrorCode $code): bool
    {
        return in_array($code, [
            ErrorCode::PayWebhookInvalidSignature,
            ErrorCode::PayWebhookPayloadInvalid,
            ErrorCode::PayWebhookProviderUnsupported,
            ErrorCode::PayWebhookAmountMismatch,
            ErrorCode::PayWebhookInvalidTransition,
        ], true);
    }
}
