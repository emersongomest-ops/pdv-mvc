<?php

declare(strict_types=1);

namespace App\Infrastructure\Sales\Analytics;

use App\Domain\Sales\Analytics\SaleAnalyticsRecorderInterface;
use Illuminate\Support\Facades\Log;

/** MVP sink: structured log until a real analytics read-model exists. */
final class LogSaleAnalyticsRecorder implements SaleAnalyticsRecorderInterface
{
    public function recordSaleCompleted(int $saleId, int $storeId, int $totalCents): void
    {
        Log::info('sale.completed.analytics', [
            'sale_id' => $saleId,
            'store_id' => $storeId,
            'total_cents' => $totalCents,
        ]);
    }
}
