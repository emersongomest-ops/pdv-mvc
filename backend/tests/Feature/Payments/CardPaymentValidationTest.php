<?php

declare(strict_types=1);

namespace Tests\Feature\Payments;

use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class CardPaymentValidationTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    #[Test]
    public function well_formed_card_payment_returns_method_not_implemented(): void
    {
        $saleId = $this->openSaleTotaling(1000);

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [[
                'method' => 'credit_card',
                'amount' => 10.00,
                'card' => [
                    'holder_name' => 'Maria Silva',
                    'number' => '4111111111111111',
                    'exp_month' => 12,
                    'exp_year' => 2030,
                    'indicated_person_name' => 'Maria Silva',
                    'belongs_to_indicated_person' => true,
                ],
            ]],
        ])
            ->assertStatus(501)
            ->assertJsonPath('error.code', 'PAY_METHOD_NOT_IMPLEMENTED')
            ->assertJsonPath('error.message', 'Payment method is not implemented.');
    }

    #[Test]
    public function card_payment_without_card_payload_fails_validation(): void
    {
        $saleId = $this->openSaleTotaling(1000);

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [[
                'method' => 'debit_card',
                'amount' => 10.00,
            ]],
        ])->assertStatus(422);
    }

    #[Test]
    public function card_holder_mismatch_is_rejected_before_not_implemented(): void
    {
        $saleId = $this->openSaleTotaling(1000);

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [[
                'method' => 'credit_card',
                'amount' => 10.00,
                'card' => [
                    'holder_name' => 'Maria Silva',
                    'number' => '4111111111111111',
                    'exp_month' => 12,
                    'exp_year' => 2030,
                    'indicated_person_name' => 'Joao Santos',
                    'belongs_to_indicated_person' => true,
                ],
            ]],
        ])
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'PAY_CARD_HOLDER_MISMATCH');
    }

    private function openSaleTotaling(int $unitPriceCents): int
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->withOpenShift($operator, $store);
        $product = Product::factory()->create(['base_price' => $unitPriceCents]);

        $this->actingAsOperatorAtStore($operator, $store);

        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk();

        return $saleId;
    }
}
