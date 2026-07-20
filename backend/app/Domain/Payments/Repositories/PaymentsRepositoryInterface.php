<?php

declare(strict_types=1);

namespace App\Domain\Payments\Repositories;

use App\Domain\Payments\DTOs\NormalizedPaymentWebhook;
use App\Domain\Payments\ValueObjects\PaymentLineStatus;
use App\Models\PaymentLine;
use App\Models\PaymentWebhookEvent;

interface PaymentsRepositoryInterface
{
    /**
     * @param list<array{
     *     method: \App\Domain\Payments\ValueObjects\PaymentMethod,
     *     amount: int,
     *     cash_received: ?int,
     *     change_amount: ?int,
     *     transaction_reference: ?string,
     *     status?: \App\Domain\Payments\ValueObjects\PaymentLineStatus|string
     * }> $lines
     */
    public function recordForSale(int $saleId, array $lines): void;

    public function findLineByTransactionReference(string $transactionReference): ?PaymentLine;

    public function markLineStatus(
        PaymentLine $line,
        PaymentLineStatus $status,
        ?\DateTimeInterface $confirmedAt = null,
    ): PaymentLine;

    /**
     * @return array{event: PaymentWebhookEvent, was_duplicate: bool}
     */
    public function recordWebhookEvent(NormalizedPaymentWebhook $webhook): array;

    public function markWebhookProcessed(PaymentWebhookEvent $event): void;

    /**
     * @return list<PaymentLine>
     */
    public function listPendingLines(?int $storeId = null): array;

    public function countPendingLines(?int $storeId = null): int;
}
