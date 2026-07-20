<?php

declare(strict_types=1);

namespace App\Domain\Sales\Events;

/**
 * Raised after a sale is committed on the critical path (post-transaction).
 */
final readonly class SaleCompleted
{
    public function __construct(
        public int $saleId,
        public int $storeId,
        public int $operatorId,
        public int $totalCents,
        public ?int $customerId = null,
    ) {}
}
