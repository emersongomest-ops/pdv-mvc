<?php

declare(strict_types=1);

namespace Tests\Feature\Payments;

use App\Domain\Payments\ValueObjects\PaymentLineStatus;
use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Domain\Payments\Webhooks\WebhookRetryQueueInterface;
use App\Models\PaymentLine;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class PaymentReconcileTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    #[Test]
    public function pix_complete_sale_enqueues_pending_and_reconcile_confirms(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = \App\Models\Product::factory()->create(['base_price' => 1000]);
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

        $line = PaymentLine::query()->where('sale_id', $saleId)->firstOrFail();
        $this->assertSame(PaymentLineStatus::Pending, $line->status);

        $this->postJson('/api/operational/payments/reconcile')
            ->assertOk()
            ->assertJsonPath('data.settlements_confirmed', 1)
            ->assertJsonPath('data.still_pending', 0);

        $this->assertSame(PaymentLineStatus::Confirmed, $line->fresh()->status);
    }

    #[Test]
    public function unknown_webhook_reference_is_queued_and_reconcile_retries(): void
    {
        $payload = [
            'event_id' => 'evt_early',
            'type' => 'payment.confirmed',
            'transaction_reference' => 'soap-stub-late',
            'amount' => 500,
        ];
        $raw = json_encode($payload, JSON_THROW_ON_ERROR);
        $secret = (string) config('payments.webhook.secret');
        $signature = 'sha256='.hash_hmac('sha256', $raw, $secret);

        $this->call(
            'POST',
            '/api/webhooks/payments/stub',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X_PAYMENT_WEBHOOK_SIGNATURE' => $signature,
            ],
            content: $raw,
        )->assertNotFound()
            ->assertJsonPath('error.code', 'PAY_WEBHOOK_UNKNOWN_REFERENCE');

        /** @var WebhookRetryQueueInterface $queue */
        $queue = $this->app->make(WebhookRetryQueueInterface::class);
        $this->assertSame(1, $queue->size());

        $store = Store::factory()->create();
        $sale = Sale::factory()->create(['store_id' => $store->id]);
        PaymentLine::query()->create([
            'sale_id' => $sale->id,
            'method' => PaymentMethod::Pix,
            'amount' => 500,
            'cash_received' => null,
            'change_amount' => null,
            'transaction_reference' => 'soap-stub-late',
            'status' => PaymentLineStatus::Pending,
            'confirmed_at' => null,
        ]);

        $manager = User::factory()->manager()->create();
        $this->actingAs($manager)
            ->postJson('/api/admin/payments/reconcile')
            ->assertOk()
            ->assertJsonPath('data.webhook_retries_succeeded', 1);

        $this->assertSame(0, $queue->size());
        $this->assertSame(
            PaymentLineStatus::Confirmed,
            PaymentLine::query()->where('transaction_reference', 'soap-stub-late')->firstOrFail()->status,
        );
    }

    #[Test]
    public function artisan_payments_reconcile_runs(): void
    {
        $this->artisan('payments:reconcile')
            ->assertSuccessful();
    }
}
