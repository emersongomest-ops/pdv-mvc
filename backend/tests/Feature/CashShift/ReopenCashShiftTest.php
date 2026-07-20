<?php

declare(strict_types=1);

namespace Tests\Feature\CashShift;

use App\Domain\Audit\ValueObjects\AuditAction;
use App\Domain\CashShift\ValueObjects\CashShiftStatus;
use App\Models\AuditLog;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\ActsWithAdminStoreAccess;
use Tests\Support\ActsWithOperationalSession;
use Tests\TestCase;

final class ReopenCashShiftTest extends TestCase
{
    use ActsWithAdminStoreAccess;
    use ActsWithOperationalSession;
    use RefreshDatabase;

    #[Test]
    public function manager_can_reopen_closed_shift_and_audit_is_written(): void
    {
        $store = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $operator = User::factory()->operator()->create();
        $shift = $this->withOpenShift($operator, $store);
        $shift->update([
            'status' => CashShiftStatus::Closed,
            'closing_cash_amount' => 15000,
            'closed_at' => now(),
        ]);

        $response = $this
            ->actingAsManagerForStore($manager, $store)
            ->postJson("/api/admin/shifts/{$shift->id}/reopen");

        $response
            ->assertOk()
            ->assertJsonPath('data.message', 'Cash shift reopened.')
            ->assertJsonPath('data.shift.id', $shift->id)
            ->assertJsonPath('data.shift.status', 'open')
            ->assertJsonPath('data.shift.closing_cash_amount', null);

        $this->assertDatabaseHas('cash_shifts', [
            'id' => $shift->id,
            'status' => CashShiftStatus::Open->value,
            'closing_cash_amount' => null,
            'closed_at' => null,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => AuditAction::CashShiftReopened->value,
            'actor_user_id' => $manager->id,
            'subject_type' => 'cash_shift',
            'subject_id' => $shift->id,
            'store_id' => $store->id,
        ]);

        $log = AuditLog::query()->where('action', AuditAction::CashShiftReopened->value)->first();
        $this->assertNotNull($log);
        $this->assertSame('closed', $log->old_values['status'] ?? null);
        $this->assertSame('open', $log->new_values['status'] ?? null);
    }

    #[Test]
    public function reopen_denied_when_operator_already_has_open_shift(): void
    {
        $store = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $operator = User::factory()->operator()->create();
        $closed = $this->withOpenShift($operator, $store);
        $closed->update([
            'status' => CashShiftStatus::Closed,
            'closing_cash_amount' => 1000,
            'closed_at' => now(),
        ]);
        $this->withOpenShift($operator, $store);

        $this
            ->actingAsManagerForStore($manager, $store)
            ->postJson("/api/admin/shifts/{$closed->id}/reopen")
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'SHIFT_ALREADY_OPEN');
    }

    #[Test]
    public function reopen_open_shift_returns_already_open(): void
    {
        $store = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $operator = User::factory()->operator()->create();
        $shift = $this->withOpenShift($operator, $store);

        $this
            ->actingAsManagerForStore($manager, $store)
            ->postJson("/api/admin/shifts/{$shift->id}/reopen")
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'SHIFT_ALREADY_OPEN');
    }

    #[Test]
    public function operator_cannot_reopen_shift(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $shift = $this->withOpenShift($operator, $store);
        $shift->update([
            'status' => CashShiftStatus::Closed,
            'closed_at' => now(),
        ]);

        $this
            ->actingAs($operator)
            ->postJson("/api/admin/shifts/{$shift->id}/reopen")
            ->assertForbidden();
    }

    #[Test]
    public function manager_without_store_assignment_cannot_reopen(): void
    {
        $store = Store::factory()->create();
        $other = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $this->attachManagerToStore($manager, $other);
        $operator = User::factory()->operator()->create();
        $shift = $this->withOpenShift($operator, $store);
        $shift->update([
            'status' => CashShiftStatus::Closed,
            'closed_at' => now(),
        ]);

        $this
            ->actingAs($manager)
            ->postJson("/api/admin/shifts/{$shift->id}/reopen")
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_STORE_ACCESS_DENIED');
    }

    #[Test]
    public function unknown_shift_returns_not_found(): void
    {
        $manager = User::factory()->manager()->create();

        $this
            ->actingAs($manager)
            ->postJson('/api/admin/shifts/99999/reopen')
            ->assertNotFound()
            ->assertJsonPath('error.code', 'SHIFT_NOT_FOUND');
    }
}
