<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Domain\Sales\ValueObjects\SaleStatus;
use App\Models\CashShift;
use App\Models\PaymentLine;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\ActsWithAdminStoreAccess;
use Tests\TestCase;

final class RefundThrottleAndSalesIdorTest extends TestCase
{
    use ActsWithAdminStoreAccess;
    use RefreshDatabase;

    #[Test]
    public function operator_cannot_list_or_show_admin_sales(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $operator->stores()->attach($store);
        $sale = Sale::factory()->completed()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
        ]);

        $this->actingAs($operator)
            ->getJson('/api/admin/sales')
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_ADMIN_ONLY');

        $this->actingAs($operator)
            ->getJson("/api/admin/sales/{$sale->id}")
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_ADMIN_ONLY');
    }

    #[Test]
    public function manager_cannot_show_sale_from_unassigned_store(): void
    {
        $storeA = Store::factory()->create();
        $storeB = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $this->attachManagerToStore($manager, $storeA);

        $saleB = Sale::factory()->completed()->create(['store_id' => $storeB->id]);

        $this->actingAs($manager)
            ->getJson("/api/admin/sales/{$saleB->id}")
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_STORE_ACCESS_DENIED');
    }

    #[Test]
    public function refund_endpoint_is_rate_limited(): void
    {
        $ctx = $this->completedSaleFixture();
        $manager = $ctx['manager'];
        $saleId = $ctx['sale_id'];

        RateLimiter::clear($manager->id.'|127.0.0.1');

        for ($i = 0; $i < 10; $i++) {
            $response = $this->actingAs($manager)->postJson("/api/admin/sales/{$saleId}/refunds", [
                'type' => 'partial_refund',
                'reason' => 'throttle-test-'.$i,
                'lines' => [
                    ['sale_line_id' => $ctx['line_id'], 'quantity' => 1],
                ],
            ]);

            $this->assertNotSame(429, $response->status(), 'unexpected throttle before limit');
        }

        $this->actingAs($manager)->postJson("/api/admin/sales/{$saleId}/refunds", [
            'type' => 'partial_refund',
            'reason' => 'throttle-overflow',
            'lines' => [
                ['sale_line_id' => $ctx['line_id'], 'quantity' => 1],
            ],
        ])->assertStatus(429);
    }

    /**
     * @return array{manager: User, sale_id: int, line_id: int}
     */
    private function completedSaleFixture(): array
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $manager = User::factory()->manager()->create();
        $operator->stores()->attach($store);
        $manager->stores()->attach($store);

        $shift = CashShift::factory()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
        ]);

        $product = Product::factory()->create(['base_price' => 1000]);
        StoreInventory::factory()->create([
            'store_id' => $store->id,
            'product_id' => $product->id,
            'quantity' => 50,
        ]);

        $sale = Sale::factory()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'cash_shift_id' => $shift->id,
            'status' => SaleStatus::Completed,
            'subtotal' => 1000,
            'discount_total' => 0,
            'total' => 1000,
            'completed_at' => now(),
        ]);

        $line = SaleLine::query()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 1000,
            'line_discount' => 0,
            'line_total' => 1000,
        ]);

        PaymentLine::query()->create([
            'sale_id' => $sale->id,
            'method' => PaymentMethod::Cash,
            'amount' => 1000,
            'transaction_reference' => 'cash-ref-throttle',
        ]);

        return [
            'manager' => $manager,
            'sale_id' => $sale->id,
            'line_id' => $line->id,
        ];
    }
}
