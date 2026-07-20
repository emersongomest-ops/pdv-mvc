<?php

declare(strict_types=1);

namespace Tests\Feature\CashShift;

use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Domain\Sales\ValueObjects\SaleStatus;
use App\Models\PaymentLine;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class CloseCashShiftReportTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    #[Test]
    public function close_shift_returns_consolidated_report_with_cash_variance(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $shift = $this->withOpenShift($operator, $store, openingCents: 10000);

        $saleCash = Sale::factory()->completed()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'cash_shift_id' => $shift->id,
            'status' => SaleStatus::Completed,
            'subtotal' => 5000,
            'total' => 5000,
        ]);
        PaymentLine::query()->create([
            'sale_id' => $saleCash->id,
            'method' => PaymentMethod::Cash,
            'amount' => 5000,
        ]);

        $salePix = Sale::factory()->completed()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'cash_shift_id' => $shift->id,
            'status' => SaleStatus::Completed,
            'subtotal' => 3000,
            'total' => 3000,
        ]);
        PaymentLine::query()->create([
            'sale_id' => $salePix->id,
            'method' => PaymentMethod::Pix,
            'amount' => 3000,
        ]);

        // expected = 100.00 + 50.00 cash = 150.00; closing 140.00 → variance -10.00
        $response = $this
            ->actingAsOperatorAtStore($operator, $store)
            ->postJson('/api/operational/shifts/close', [
                'closing_cash_amount' => 140.00,
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.report.sales_count', 2)
            ->assertJsonPath('data.report.sales_total', '80.00')
            ->assertJsonPath('data.report.opening_cash_amount', '100.00')
            ->assertJsonPath('data.report.expected_cash_amount', '150.00')
            ->assertJsonPath('data.report.closing_cash_amount', '140.00')
            ->assertJsonPath('data.report.cash_variance', '-10.00')
            ->assertJsonPath('data.report.totals_by_payment_method.0.method', 'cash')
            ->assertJsonPath('data.report.totals_by_payment_method.0.amount', '50.00')
            ->assertJsonPath('data.report.totals_by_payment_method.1.method', 'pix')
            ->assertJsonPath('data.report.totals_by_payment_method.1.amount', '30.00');
    }

    #[Test]
    public function close_without_closing_cash_leaves_variance_null(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->withOpenShift($operator, $store);

        $this
            ->actingAsOperatorAtStore($operator, $store)
            ->postJson('/api/operational/shifts/close')
            ->assertOk()
            ->assertJsonPath('data.report.closing_cash_amount', null)
            ->assertJsonPath('data.report.cash_variance', null);
    }
}
