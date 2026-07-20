<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Sales\Orchestration;

use App\Application\Sales\Orchestration\DispatchSaleSideEffects;
use App\Application\Shared\Orchestration\BusOrchestrator;
use App\Jobs\Sales\RecordCustomerPurchaseJob;
use App\Jobs\Sales\RecordSaleCompletedAnalyticsJob;
use App\Models\Sale;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DispatchSaleSideEffectsTest extends TestCase
{
    #[Test]
    public function dispatches_analytics_and_customer_jobs_in_named_batch(): void
    {
        Bus::fake();

        $sale = new Sale;
        $sale->id = 42;
        $sale->customer_id = 7;
        $sale->total = 3000;

        (new DispatchSaleSideEffects(new BusOrchestrator))->dispatch($sale, storeId: 3);

        Bus::assertBatched(function (PendingBatch $batch): bool {
            if ($batch->name !== 'sale-42-side-effects') {
                return false;
            }

            $jobs = $batch->jobs;

            return $jobs->contains(fn ($job): bool => $job instanceof RecordSaleCompletedAnalyticsJob
                    && $job->saleId === 42
                    && $job->storeId === 3
                    && $job->totalCents === 3000)
                && $jobs->contains(fn ($job): bool => $job instanceof RecordCustomerPurchaseJob
                    && $job->customerId === 7
                    && $job->storeId === 3
                    && $job->amountCents === 3000
                    && $job->saleId === 42);
        });
    }

    #[Test]
    public function omits_customer_job_when_sale_has_no_customer(): void
    {
        Bus::fake();

        $sale = new Sale;
        $sale->id = 10;
        $sale->customer_id = null;
        $sale->total = 1000;

        (new DispatchSaleSideEffects(new BusOrchestrator))->dispatch($sale, storeId: 1);

        Bus::assertBatched(function (PendingBatch $batch): bool {
            return $batch->name === 'sale-10-side-effects'
                && $batch->jobs->count() === 1
                && $batch->jobs->first() instanceof RecordSaleCompletedAnalyticsJob;
        });
    }
}
