<?php

declare(strict_types=1);

namespace App\Jobs\Sales;

use App\Domain\Sales\Analytics\SaleAnalyticsRecorderInterface;
use App\Jobs\AbstractQueuedJob;

/** Records non-critical sale analytics after commit (ADR-0006). */
final class RecordSaleCompletedAnalyticsJob extends AbstractQueuedJob
{
    public function __construct(
        public readonly int $saleId,
        public readonly int $storeId,
        public readonly int $totalCents,
    ) {}

    public function handle(SaleAnalyticsRecorderInterface $analytics): void
    {
        $analytics->recordSaleCompleted(
            $this->saleId,
            $this->storeId,
            $this->totalCents,
        );
    }
}
