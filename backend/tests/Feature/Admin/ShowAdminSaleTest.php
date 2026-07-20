<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Domain\Sales\ValueObjects\SaleStatus;
use App\Models\CashShift;
use App\Models\PaymentLine;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\ActsWithAdminStoreAccess;
use Tests\TestCase;

final class ShowAdminSaleTest extends TestCase
{
    use ActsWithAdminStoreAccess;
    use RefreshDatabase;

    #[Test]
    public function manager_can_show_sale_for_refund_lookup(): void
    {
        $store = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $this->attachManagerToStore($manager, $store);
        $operator = User::factory()->operator()->create();
        $shift = CashShift::factory()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
        ]);
        $product = Product::factory()->create(['base_price' => 2500]);

        $sale = Sale::factory()->completed()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'cash_shift_id' => $shift->id,
            'subtotal' => 2500,
            'total' => 2500,
            'status' => SaleStatus::Completed,
        ]);
        SaleLine::query()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 2500,
            'line_discount' => 0,
            'line_total' => 2500,
        ]);
        PaymentLine::query()->create([
            'sale_id' => $sale->id,
            'method' => PaymentMethod::Pix,
            'amount' => 2500,
        ]);

        $this->actingAs($manager)
            ->getJson("/api/admin/sales/{$sale->id}")
            ->assertOk()
            ->assertJsonPath('data.sale.id', $sale->id)
            ->assertJsonPath('data.sale.status', 'completed')
            ->assertJsonPath('data.sale.total', '25.00')
            ->assertJsonPath('data.sale.lines.0.product_id', $product->id);
    }

    #[Test]
    public function manager_gets_not_found_for_unknown_sale(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/sales/99999')
            ->assertNotFound();
    }

    #[Test]
    public function operator_cannot_show_admin_sale(): void
    {
        $operator = User::factory()->operator()->create();
        $sale = Sale::factory()->completed()->create();

        $this->actingAs($operator)
            ->getJson("/api/admin/sales/{$sale->id}")
            ->assertForbidden();
    }
}
