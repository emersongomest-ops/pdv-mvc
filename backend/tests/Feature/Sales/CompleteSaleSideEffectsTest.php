<?php

declare(strict_types=1);

namespace Tests\Feature\Sales;

use App\Domain\Sales\Analytics\SaleAnalyticsRecorderInterface;
use App\Jobs\Sales\RecordCustomerPurchaseJob;
use App\Jobs\Sales\RecordSaleCompletedAnalyticsJob;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class CompleteSaleSideEffectsTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    #[Test]
    public function complete_sale_dispatches_parallel_side_effect_batch(): void
    {
        Bus::fake();

        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $customer = Customer::factory()->create(['lifetime_spend' => 0]);
        $product = Product::factory()->create(['base_price' => 1000]);

        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/customer", [
            'customer_id' => $customer->id,
        ])->assertOk();

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'pix', 'amount' => 10.00],
            ],
        ])->assertOk();

        Bus::assertBatched(function ($batch) use ($saleId, $store, $customer): bool {
            return $batch->name === "sale-{$saleId}-side-effects"
                && $batch->jobs->contains(
                    fn ($job): bool => $job instanceof RecordSaleCompletedAnalyticsJob
                        && $job->saleId === $saleId
                        && $job->storeId === $store->id,
                )
                && $batch->jobs->contains(
                    fn ($job): bool => $job instanceof RecordCustomerPurchaseJob
                        && $job->customerId === $customer->id
                        && $job->saleId === $saleId,
                );
        });
    }

    #[Test]
    public function complete_sale_runs_analytics_job_when_queue_is_sync(): void
    {
        $recorder = new class implements SaleAnalyticsRecorderInterface
        {
            /** @var list<array{sale_id: int, store_id: int, total_cents: int}> */
            public array $events = [];

            public function recordSaleCompleted(int $saleId, int $storeId, int $totalCents): void
            {
                $this->events[] = [
                    'sale_id' => $saleId,
                    'store_id' => $storeId,
                    'total_cents' => $totalCents,
                ];
            }
        };

        $this->app->instance(SaleAnalyticsRecorderInterface::class, $recorder);

        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create(['base_price' => 1000]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'pix', 'amount' => 10.00],
            ],
        ])->assertOk();

        $this->assertCount(1, $recorder->events);
        $this->assertSame([
            'sale_id' => $saleId,
            'store_id' => $store->id,
            'total_cents' => 1000,
        ], $recorder->events[0]);
    }
}
