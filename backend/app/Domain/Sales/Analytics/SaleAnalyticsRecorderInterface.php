<?php

declare(strict_types=1);

namespace App\Domain\Sales\Analytics;

/**
 * Post-sale analytics sink (non-critical; ADR-0006 side effects).
 */
interface SaleAnalyticsRecorderInterface
{
    public function recordSaleCompleted(int $saleId, int $storeId, int $totalCents): void;
}
