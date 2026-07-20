<?php

declare(strict_types=1);

namespace App\Infrastructure\Payments\Persistence\Repositories;

use App\Domain\Payments\DTOs\NormalizedPaymentWebhook;
use App\Domain\Payments\Repositories\PaymentsRepositoryInterface;
use App\Domain\Payments\ValueObjects\PaymentLineStatus;
use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Models\PaymentLine;
use App\Models\PaymentWebhookEvent;
use Illuminate\Database\UniqueConstraintViolationException;

final class PaymentsRepository implements PaymentsRepositoryInterface
{
    public function recordForSale(int $saleId, array $lines): void
    {
        foreach ($lines as $line) {
            $status = $line['status'] ?? PaymentLineStatus::Confirmed;
            if (! $status instanceof PaymentLineStatus) {
                $status = PaymentLineStatus::from((string) $status);
            }

            PaymentLine::query()->create([
                'sale_id' => $saleId,
                'method' => $line['method'] instanceof PaymentMethod
                    ? $line['method']
                    : PaymentMethod::from((string) $line['method']),
                'amount' => $line['amount'],
                'cash_received' => $line['cash_received'],
                'change_amount' => $line['change_amount'],
                'transaction_reference' => $line['transaction_reference'],
                'status' => $status,
                'confirmed_at' => $status === PaymentLineStatus::Confirmed ? now() : null,
            ]);
        }
    }

    public function findLineByTransactionReference(string $transactionReference): ?PaymentLine
    {
        return PaymentLine::query()
            ->where('transaction_reference', $transactionReference)
            ->first();
    }

    public function markLineStatus(
        PaymentLine $line,
        PaymentLineStatus $status,
        ?\DateTimeInterface $confirmedAt = null,
    ): PaymentLine {
        $line->status = $status;
        $line->confirmed_at = $status === PaymentLineStatus::Confirmed
            ? ($confirmedAt ?? now())
            : null;
        $line->save();

        return $line->refresh();
    }

    public function recordWebhookEvent(NormalizedPaymentWebhook $webhook): array
    {
        try {
            $event = PaymentWebhookEvent::query()->create([
                'provider' => $webhook->provider,
                'provider_event_id' => $webhook->providerEventId,
                'event_type' => $webhook->type->value,
                'transaction_reference' => $webhook->transactionReference,
                'payload' => $webhook->rawPayload,
                'processing_status' => 'received',
            ]);

            return ['event' => $event, 'was_duplicate' => false];
        } catch (UniqueConstraintViolationException) {
            $event = PaymentWebhookEvent::query()
                ->where('provider', $webhook->provider)
                ->where('provider_event_id', $webhook->providerEventId)
                ->firstOrFail();

            return ['event' => $event, 'was_duplicate' => true];
        }
    }

    public function markWebhookProcessed(PaymentWebhookEvent $event): void
    {
        $event->processing_status = 'processed';
        $event->processed_at = now();
        $event->save();
    }

    public function listPendingLines(?int $storeId = null): array
    {
        $query = PaymentLine::query()
            ->where('status', PaymentLineStatus::Pending)
            ->orderBy('id');

        if ($storeId !== null) {
            $query->whereHas('sale', static function ($sale) use ($storeId): void {
                $sale->where('store_id', $storeId);
            });
        }

        return $query->get()->all();
    }

    public function countPendingLines(?int $storeId = null): int
    {
        $query = PaymentLine::query()->where('status', PaymentLineStatus::Pending);

        if ($storeId !== null) {
            $query->whereHas('sale', static function ($sale) use ($storeId): void {
                $sale->where('store_id', $storeId);
            });
        }

        return $query->count();
    }
}
