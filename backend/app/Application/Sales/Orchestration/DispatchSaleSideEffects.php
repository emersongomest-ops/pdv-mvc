<?php

declare(strict_types=1);

namespace App\Application\Sales\Orchestration;

use App\Application\Shared\Orchestration\BusOrchestrator;
use App\Jobs\Sales\RecordCustomerPurchaseJob;
use App\Jobs\Sales\RecordSaleCompletedAnalyticsJob;
use App\Models\Sale;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Dispatches parallel post-sale side effects after the critical path commits.
 *
 * Batch (not chain): customer stats and analytics are independent (ADR-0006).
 * Failures are reported without rolling back the completed sale.
 */
final class DispatchSaleSideEffects
{
    public function __construct(
        private readonly BusOrchestrator $bus,
    ) {}

    public function dispatch(Sale $sale, int $storeId): void
    {
        $totalCents = (int) $sale->total;

        $jobs = [
            new RecordSaleCompletedAnalyticsJob($sale->id, $storeId, $totalCents),
        ];

        if ($sale->customer_id !== null) {
            $jobs[] = new RecordCustomerPurchaseJob(
                $sale->customer_id,
                $storeId,
                $totalCents,
                $sale->id,
            );
        }

        try {
            $this->bus->dispatchBatch(
                $jobs,
                name: "sale-{$sale->id}-side-effects",
                catch: static function ($batch, Throwable $e): void {
                    Log::error('sale.side_effects.batch_failed', [
                        'batch_id' => $batch->id,
                        'message' => $e->getMessage(),
                    ]);
                },
            );
        } catch (Throwable $e) {
            // Sale already committed — never fail the HTTP critical path.
            report($e);
        }
    }
}
