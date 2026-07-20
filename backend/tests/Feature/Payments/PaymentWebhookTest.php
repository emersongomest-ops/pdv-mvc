<?php

declare(strict_types=1);

namespace Tests\Feature\Payments;

use App\Domain\Payments\ValueObjects\PaymentLineStatus;
use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Models\PaymentLine;
use App\Models\PaymentWebhookEvent;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function confirms_pending_payment_line_with_valid_signature(): void
    {
        $line = $this->pendingPaymentLine('stub-ref-001', 1500);

        $payload = [
            'event_id' => 'evt_1',
            'type' => 'payment.confirmed',
            'transaction_reference' => 'stub-ref-001',
            'amount' => 1500,
        ];

        $response = $this->postSignedWebhook('stub', $payload);

        $response->assertAccepted()
            ->assertJsonPath('data.duplicate', false)
            ->assertJsonPath('data.payment_status', 'confirmed')
            ->assertJsonPath('data.payment_line_id', $line->id);

        $this->assertSame(PaymentLineStatus::Confirmed, $line->fresh()->status);
        $this->assertNotNull($line->fresh()->confirmed_at);
        $this->assertDatabaseHas('payment_webhook_events', [
            'provider' => 'stub',
            'provider_event_id' => 'evt_1',
            'processing_status' => 'processed',
        ]);
    }

    #[Test]
    public function duplicate_event_is_idempotent(): void
    {
        $this->pendingPaymentLine('stub-ref-002', 2000);

        $payload = [
            'event_id' => 'evt_dup',
            'type' => 'payment.confirmed',
            'transaction_reference' => 'stub-ref-002',
            'amount' => 2000,
        ];

        $this->postSignedWebhook('stub', $payload)->assertAccepted();
        $second = $this->postSignedWebhook('stub', $payload);

        $second->assertOk()->assertJsonPath('data.duplicate', true);
        $this->assertSame(1, PaymentWebhookEvent::query()->where('provider_event_id', 'evt_dup')->count());
        $this->assertSame(
            PaymentLineStatus::Confirmed,
            PaymentLine::query()->where('transaction_reference', 'stub-ref-002')->firstOrFail()->status,
        );
    }

    #[Test]
    public function invalid_signature_is_rejected(): void
    {
        $this->pendingPaymentLine('stub-ref-003', 1000);

        $payload = [
            'event_id' => 'evt_bad_sig',
            'type' => 'payment.confirmed',
            'transaction_reference' => 'stub-ref-003',
        ];
        $raw = json_encode($payload, JSON_THROW_ON_ERROR);

        $this->call(
            'POST',
            '/api/webhooks/payments/stub',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X_PAYMENT_WEBHOOK_SIGNATURE' => 'sha256=deadbeef',
            ],
            content: $raw,
        )->assertUnauthorized()
            ->assertJsonPath('error.code', 'PAY_WEBHOOK_INVALID_SIGNATURE');
    }

    #[Test]
    public function unknown_transaction_reference_returns_not_found(): void
    {
        $payload = [
            'event_id' => 'evt_missing',
            'type' => 'payment.confirmed',
            'transaction_reference' => 'no-such-ref',
        ];

        $this->postSignedWebhook('stub', $payload)
            ->assertNotFound()
            ->assertJsonPath('error.code', 'PAY_WEBHOOK_UNKNOWN_REFERENCE');
    }

    #[Test]
    public function failed_webhook_marks_pending_line_as_failed(): void
    {
        $line = $this->pendingPaymentLine('stub-ref-fail', 900);

        $payload = [
            'event_id' => 'evt_fail',
            'type' => 'payment.failed',
            'transaction_reference' => 'stub-ref-fail',
        ];

        $this->postSignedWebhook('stub', $payload)
            ->assertAccepted()
            ->assertJsonPath('data.payment_status', 'failed');

        $this->assertSame(PaymentLineStatus::Failed, $line->fresh()->status);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function postSignedWebhook(string $provider, array $payload): \Illuminate\Testing\TestResponse
    {
        $raw = json_encode($payload, JSON_THROW_ON_ERROR);
        $secret = (string) config('payments.webhook.secret');
        $signature = 'sha256='.hash_hmac('sha256', $raw, $secret);

        return $this->call(
            'POST',
            '/api/webhooks/payments/'.$provider,
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X_PAYMENT_WEBHOOK_SIGNATURE' => $signature,
            ],
            content: $raw,
        );
    }

    private function pendingPaymentLine(string $reference, int $amountCents): PaymentLine
    {
        $store = Store::factory()->create();
        $user = User::factory()->operator()->create();
        $user->stores()->attach($store);

        $sale = Sale::factory()->create([
            'store_id' => $store->id,
            'user_id' => $user->id,
            'total' => $amountCents,
        ]);

        return PaymentLine::query()->create([
            'sale_id' => $sale->id,
            'method' => PaymentMethod::Pix,
            'amount' => $amountCents,
            'cash_received' => null,
            'change_amount' => null,
            'transaction_reference' => $reference,
            'status' => PaymentLineStatus::Pending,
            'confirmed_at' => null,
        ]);
    }
}
