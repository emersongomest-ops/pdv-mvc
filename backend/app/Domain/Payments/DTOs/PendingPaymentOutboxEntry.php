<?php

declare(strict_types=1);

namespace App\Domain\Payments\DTOs;

/** Pending payment awaiting webhook or reconcile poll (Option A outbox). */
final readonly class PendingPaymentOutboxEntry
{
    public function __construct(
        public string $transactionReference,
        public int $paymentLineId,
        public int $saleId,
        public ?int $storeId,
        public string $provider,
        public int $attempts,
        public string $enqueuedAt,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'transaction_reference' => $this->transactionReference,
            'payment_line_id' => $this->paymentLineId,
            'sale_id' => $this->saleId,
            'store_id' => $this->storeId,
            'provider' => $this->provider,
            'attempts' => $this->attempts,
            'enqueued_at' => $this->enqueuedAt,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            transactionReference: (string) $data['transaction_reference'],
            paymentLineId: (int) $data['payment_line_id'],
            saleId: (int) $data['sale_id'],
            storeId: isset($data['store_id']) ? (int) $data['store_id'] : null,
            provider: (string) ($data['provider'] ?? 'stub'),
            attempts: (int) ($data['attempts'] ?? 0),
            enqueuedAt: (string) ($data['enqueued_at'] ?? now()->toIso8601String()),
        );
    }
}
