<?php

declare(strict_types=1);

namespace Tests\Feature\Audit;

use App\Domain\Audit\DTOs\AdminAuditFilters;
use App\Domain\Audit\DTOs\AuditLogEntry;
use App\Domain\Audit\Repositories\AuditLogRepositoryInterface;
use App\Domain\Audit\ValueObjects\AuditAction;
use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Domain\Sales\ValueObjects\SaleStatus;
use App\Models\AuditLog;
use App\Models\CashShift;
use App\Models\Customer;
use App\Models\PaymentLine;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\Support\ActsWithAdminStoreAccess;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class AdminAuditLogTest extends TestCase
{
    use ActsWithAdminStoreAccess;
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    #[Test]
    public function price_change_creates_audit_log_and_same_price_does_not(): void
    {
        $manager = User::factory()->manager()->create();
        $product = Product::factory()->create(['base_price' => 1000, 'sku' => 'P1']);

        $this->actingAs($manager)->patchJson("/api/admin/catalog/products/{$product->id}", [
            'base_price' => 12.50,
        ])->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'action' => AuditAction::ProductPriceChanged->value,
            'actor_user_id' => $manager->id,
            'subject_type' => 'product',
            'subject_id' => $product->id,
        ]);

        $this->actingAs($manager)->patchJson("/api/admin/catalog/products/{$product->id}", [
            'name' => 'Renamed',
            'base_price' => 12.50,
        ])->assertOk();

        $this->assertSame(1, AuditLog::query()->count());
    }

    #[Test]
    public function stock_adjust_and_refund_create_store_scoped_audit_logs(): void
    {
        $store = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $this->attachManagerToStore($manager, $store);
        $product = Product::factory()->create(['base_price' => 1000]);

        $this->actingAs($manager)->postJson('/api/admin/inventory/adjustments', [
            'store_id' => $store->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'reason' => 'Initial count',
        ])->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'action' => AuditAction::StockAdjusted->value,
            'store_id' => $store->id,
            'subject_id' => $product->id,
        ]);

        $operator = User::factory()->operator()->create();
        $shift = CashShift::factory()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
        ]);
        $sale = Sale::factory()->completed()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'cash_shift_id' => $shift->id,
            'status' => SaleStatus::Completed,
            'subtotal' => 1000,
            'total' => 1000,
        ]);
        SaleLine::query()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 1000,
            'line_discount' => 0,
            'line_total' => 1000,
        ]);
        PaymentLine::query()->create([
            'sale_id' => $sale->id,
            'method' => PaymentMethod::Pix,
            'amount' => 1000,
            'transaction_reference' => 'ref-1',
        ]);

        $this->actingAs($manager)->postJson("/api/admin/sales/{$sale->id}/refunds", [
            'type' => 'partial_refund',
            'reason' => 'Customer complaint',
            'lines' => [['sale_line_id' => SaleLine::query()->firstOrFail()->id, 'quantity' => 1]],
        ])->assertCreated();

        $this->assertDatabaseHas('audit_logs', [
            'action' => AuditAction::RefundCreated->value,
            'store_id' => $store->id,
            'actor_user_id' => $manager->id,
        ]);

        $this->actingAs($manager)->postJson("/api/admin/sales/{$sale->id}/refunds", [
            'type' => 'full_return',
            'reason' => 'Already refunded should fail',
        ])->assertStatus(409);

        $this->assertSame(1, AuditLog::query()->where('action', AuditAction::RefundCreated->value)->count());
        $this->assertSame(0, AuditLog::query()->where('action', AuditAction::ReturnCreated->value)->count());
    }

    #[Test]
    public function promotion_management_is_audited_but_apply_on_sale_is_not(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)->postJson('/api/admin/promotions', [
            'code' => 'AUDIT10',
            'name' => 'Audit 10%',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'stacking_mode' => 'accumulable',
            'applies_to_all_customers' => true,
        ])->assertCreated();

        $managed = Promotion::query()->where('code', 'AUDIT10')->firstOrFail();

        $this->assertDatabaseHas('audit_logs', [
            'action' => AuditAction::PromotionCreated->value,
            'subject_id' => $managed->id,
            'store_id' => null,
        ]);

        $this->actingAs($manager)->patchJson("/api/admin/promotions/{$managed->id}", [
            'is_active' => false,
        ])->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'action' => AuditAction::PromotionUpdated->value,
            'subject_id' => $managed->id,
        ]);

        // Separate active promo for operational apply — never reuse deactivated AUDIT10
        // (inactive → PromoNotApplicable 422).
        $applyPromo = Promotion::factory()->create([
            'code' => 'APPLYNOAUD',
            'discount_type' => 'percent',
            'discount_value' => 1000,
            'applies_to_all_customers' => true,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
        ]);

        $beforeApply = AuditLog::query()->count();
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAsOperatorWithOpenShift($operator, $store);

        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['base_price' => 10000]);

        $saleId = (int) $this->postJson('/api/operational/sales')
            ->assertCreated()
            ->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/customer", [
            'customer_id' => $customer->id,
        ])->assertOk();

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->postJson("/api/operational/sales/{$saleId}/promotions", [
            'code' => 'APPLYNOAUD',
        ])
            ->assertOk()
            ->assertJsonPath('data.sale.promotions.0.code', 'APPLYNOAUD');

        $this->assertSame($beforeApply, AuditLog::query()->count());
        $this->assertTrue((bool) $applyPromo->fresh()->is_active);
    }

    #[Test]
    public function audit_append_failure_rolls_back_price_change(): void
    {
        $manager = User::factory()->manager()->create();
        $product = Product::factory()->create(['base_price' => 1000]);

        $this->app->bind(AuditLogRepositoryInterface::class, static function () {
            return new class implements AuditLogRepositoryInterface
            {
                public function append(AuditLogEntry $entry): AuditLog
                {
                    throw new RuntimeException('audit write failed');
                }

                public function listForAdmin(
                    AdminAuditFilters $filters,
                    array $allowedStoreIds,
                ): array {
                    return ['items' => collect(), 'next_cursor' => null];
                }
            };
        });

        $this->actingAs($manager)->patchJson("/api/admin/catalog/products/{$product->id}", [
            'base_price' => 20.00,
        ])->assertStatus(500);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'base_price' => 1000,
        ]);
        $this->assertSame(0, AuditLog::query()->count());
    }

    #[Test]
    public function audit_logs_are_immutable(): void
    {
        $manager = User::factory()->manager()->create();
        $product = Product::factory()->create(['base_price' => 500]);

        $this->actingAs($manager)->patchJson("/api/admin/catalog/products/{$product->id}", [
            'base_price' => 9.99,
        ])->assertOk();

        $log = AuditLog::query()->firstOrFail();

        $this->expectException(\Throwable::class);
        $log->update(['action' => AuditAction::StockAdjusted->value]);
    }

    #[Test]
    public function query_builder_update_is_blocked_by_trigger(): void
    {
        $manager = User::factory()->manager()->create();
        $product = Product::factory()->create(['base_price' => 500]);

        $this->actingAs($manager)->patchJson("/api/admin/catalog/products/{$product->id}", [
            'base_price' => 8.00,
        ])->assertOk();

        $log = AuditLog::query()->firstOrFail();

        $this->expectException(\Throwable::class);
        DB::table('audit_logs')->where('id', $log->id)->update(['subject_id' => 999]);
    }

    #[Test]
    public function manager_can_list_filter_and_paginate_audit_logs(): void
    {
        $storeA = Store::factory()->create();
        $storeB = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $this->attachManagerToStore($manager, $storeA);
        $product = Product::factory()->create(['base_price' => 100]);

        $this->actingAs($manager)->patchJson("/api/admin/catalog/products/{$product->id}", [
            'base_price' => 2.00,
        ])->assertOk();

        $this->actingAs($manager)->postJson('/api/admin/inventory/adjustments', [
            'store_id' => $storeA->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'reason' => 'Count A',
        ])->assertOk();

        // Foreign store adjustment blocked; seed via another manager
        $otherManager = User::factory()->manager()->create();
        $this->attachManagerToStore($otherManager, $storeB);
        $this->actingAs($otherManager)->postJson('/api/admin/inventory/adjustments', [
            'store_id' => $storeB->id,
            'product_id' => $product->id,
            'quantity' => 7,
            'reason' => 'Count B',
        ])->assertOk();

        $list = $this->actingAs($manager)->getJson('/api/admin/audit-logs?per_page=1');
        $list->assertOk()->assertJsonCount(1, 'data.audit_logs');
        $this->assertNotNull($list->json('meta.next_cursor'));

        $page2 = $this->actingAs($manager)->getJson(
            '/api/admin/audit-logs?per_page=10&cursor='.urlencode((string) $list->json('meta.next_cursor')),
        );
        $page2->assertOk();
        $this->assertNotSame($list->json('data.audit_logs.0.id'), $page2->json('data.audit_logs.0.id'));

        $this->actingAs($manager)
            ->getJson('/api/admin/audit-logs?action=catalog.product.price_changed')
            ->assertOk()
            ->assertJsonCount(1, 'data.audit_logs')
            ->assertJsonPath('data.audit_logs.0.action', 'catalog.product.price_changed')
            ->assertJsonPath('data.audit_logs.0.old_values.base_price', '1.00')
            ->assertJsonPath('data.audit_logs.0.new_values.base_price', '2.00');

        $this->actingAs($manager)
            ->getJson('/api/admin/audit-logs?store_id='.$storeB->id)
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_STORE_ACCESS_DENIED');

        $visible = $this->actingAs($manager)->getJson('/api/admin/audit-logs');
        $visible->assertOk();
        $storeIds = collect($visible->json('data.audit_logs'))->pluck('store.id')->all();
        $this->assertNotContains($storeB->id, $storeIds);
        $this->assertContains(null, $storeIds);
    }

    #[Test]
    public function operator_and_guest_cannot_list_audit_logs(): void
    {
        $this->getJson('/api/admin/audit-logs')
            ->assertUnauthorized();

        $operator = User::factory()->operator()->create();
        $this->actingAs($operator)
            ->getJson('/api/admin/audit-logs')
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_ADMIN_ONLY');
    }

    #[Test]
    public function invalid_filters_return_validation_errors(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/audit-logs?action=not-a-real-action')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['action']);

        $this->actingAs($manager)
            ->getJson('/api/admin/audit-logs?from=2026-07-10&to=2026-07-01')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['to']);

        $this->actingAs($manager)
            ->getJson('/api/admin/audit-logs?cursor=not-a-valid-cursor')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['cursor']);
    }
}
