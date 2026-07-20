<?php

declare(strict_types=1);

namespace App\Jobs\Sales;

use App\Domain\Customers\Repositories\CustomersRepositoryInterface;
use App\Jobs\AbstractQueuedJob;

/** Updates customer lifetime / store stats after sale commit (ADR-0006). */
final class RecordCustomerPurchaseJob extends AbstractQueuedJob
{
    public function __construct(
        public readonly int $customerId,
        public readonly int $storeId,
        public readonly int $amountCents,
        public readonly int $saleId,
    ) {}

    public function handle(CustomersRepositoryInterface $customers): void
    {
        $customers->recordCompletedPurchase(
            $this->customerId,
            $this->storeId,
            $this->amountCents,
        );
    }
}
