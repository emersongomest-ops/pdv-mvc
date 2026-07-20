<?php

declare(strict_types=1);

namespace App\Http\Payments\Controllers;

use App\Application\Payments\Actions\ConsumePaymentWebhookAction;
use App\Domain\Payments\DTOs\WebhookRetryItem;
use App\Domain\Payments\Exceptions\PaymentDomainException;
use App\Domain\Payments\Webhooks\WebhookRetryQueueInterface;
use App\Domain\Shared\ErrorCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

final class ConsumePaymentWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        string $provider,
        ConsumePaymentWebhookAction $consume,
        WebhookRetryQueueInterface $retryQueue,
    ): JsonResponse {
        $rawBody = $request->getContent();
        /** @var array<string, mixed> $payload */
        $payload = $request->all();
        $signature = $request->header('X-Payment-Webhook-Signature');

        try {
            $result = $consume->execute(
                provider: $provider,
                rawBody: $rawBody,
                signatureHeader: $signature,
                payload: $payload,
            );
        } catch (PaymentDomainException $exception) {
            if ($this->shouldEnqueueRetry($exception->errorCode)) {
                $retryQueue->push(new WebhookRetryItem(
                    provider: $provider,
                    rawBody: $rawBody,
                    signatureHeader: $signature,
                    payload: $payload,
                    attempts: 0,
                    lastError: $exception->errorCode->value,
                    enqueuedAt: now()->toIso8601String(),
                ));
            }

            throw $exception;
        } catch (Throwable $exception) {
            $retryQueue->push(new WebhookRetryItem(
                provider: $provider,
                rawBody: $rawBody,
                signatureHeader: $signature,
                payload: $payload,
                attempts: 0,
                lastError: $exception->getMessage(),
                enqueuedAt: now()->toIso8601String(),
            ));

            throw $exception;
        }

        return response()->json([
            'data' => $result,
        ], $result['duplicate'] ? 200 : 202);
    }

    private function shouldEnqueueRetry(ErrorCode $code): bool
    {
        return $code === ErrorCode::PayWebhookUnknownReference;
    }
}
