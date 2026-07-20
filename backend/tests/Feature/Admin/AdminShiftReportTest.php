<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Domain\Sales\ValueObjects\SaleStatus;
use App\Models\PaymentLine;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\ActsWithAdminStoreAccess;
use Tests\Support\ActsWithOperationalSession;
use Tests\TestCase;

final class AdminShiftReportTest extends TestCase
{
    use ActsWithAdminStoreAccess;
    use ActsWithOperationalSession;
    use RefreshDatabase;

    #[Test]
    public function manager_can_list_shifts_for_store_and_view_report(): void
    {
        $store = Store::factory()->create(['code' => 'S1']);
        $manager = User::factory()->manager()->create();
        $this->attachManagerToStore($manager, $store);
        $operator = User::factory()->operator()->create(['name' => 'Op One']);
        $shift = $this->withOpenShift($operator, $store, openingCents: 10000);

        $sale = Sale::factory()->completed()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'cash_shift_id' => $shift->id,
            'status' => SaleStatus::Completed,
            'subtotal' => 2000,
            'total' => 2000,
        ]);
        PaymentLine::query()->create([
            'sale_id' => $sale->id,
            'method' => PaymentMethod::Cash,
            'amount' => 2000,
        ]);

        $shift->update([
            'status' => 'closed',
            'closing_cash_amount' => 12000,
            'closed_at' => now(),
        ]);

        $this->actingAs($manager)
            ->getJson('/api/admin/shifts?store_id='.$store->id)
            ->assertOk()
            ->assertJsonCount(1, 'data.shifts')
            ->assertJsonPath('data.shifts.0.id', $shift->id)
            ->assertJsonPath('data.shifts.0.store_code', 'S1')
            ->assertJsonPath('data.shifts.0.operator_name', 'Op One')
            ->assertJsonPath('data.shifts.0.status', 'closed');

        $this->actingAs($manager)
            ->getJson("/api/admin/shifts/{$shift->id}/report")
            ->assertOk()
            ->assertJsonPath('data.report.sales_count', 1)
            ->assertJsonPath('data.report.sales_total', '20.00')
            ->assertJsonPath('data.report.expected_cash_amount', '120.00')
            ->assertJsonPath('data.report.cash_variance', '0.00');
    }

    #[Test]
    public function manager_gets_not_found_for_unknown_shift_report(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/shifts/99999/report')
            ->assertNotFound();
    }

    #[Test]
    public function operator_cannot_access_admin_shift_endpoints(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $shift = $this->withOpenShift($operator, $store);

        $this->actingAs($operator)
            ->getJson('/api/admin/shifts?store_id='.$store->id)
            ->assertForbidden();

        $this->actingAs($operator)
            ->getJson("/api/admin/shifts/{$shift->id}/report")
            ->assertForbidden();
    }

    #[Test]
    public function list_shifts_requires_store_id(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/shifts')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['store_id']);
    }
}
