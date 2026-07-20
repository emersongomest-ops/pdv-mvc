<?php

declare(strict_types=1);

namespace App\Application\Admin\Actions;

use App\Application\Admin\DTOs\AdminDashboardMetrics;
use App\Application\Store\Support\AssertManagerStoreAccess;
use App\Domain\CashShift\ValueObjects\CashShiftStatus;
use App\Domain\Sales\ValueObjects\SaleStatus;
use App\Models\User;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\DB;

/**
 * Loads independent dashboard KPIs via Concurrency::run (ADR-0006).
 *
 * Store-scoped KPIs honor RN-064 (assigned stores only). Catalog counts stay global.
 *
 * Tests use CONCURRENCY_DRIVER=sync; production may use process/fork.
 */
final class GetAdminDashboardMetricsAction
{
    public function __construct(
        private readonly AssertManagerStoreAccess $storeAccess,
    ) {}

    public function execute(User $manager): AdminDashboardMetrics
    {
        $storeIds = $this->storeAccess->assignedStoreIds($manager);
        $completed = SaleStatus::Completed->value;
        $open = CashShiftStatus::Open->value;

        /** @var array{products_total: int, products_active: int, customers_total: int, sales_completed: int, open_shifts: int} $results */
        $results = Concurrency::run([
            'products_total' => static fn (): int => (int) DB::table('products')->count(),
            'products_active' => static fn (): int => (int) DB::table('products')->where('is_active', true)->count(),
            'customers_total' => static function () use ($storeIds): int {
                if ($storeIds === []) {
                    return 0;
                }

                return (int) DB::table('customer_store_stats')
                    ->whereIn('store_id', $storeIds)
                    ->distinct()
                    ->count('customer_id');
            },
            'sales_completed' => static function () use ($storeIds, $completed): int {
                if ($storeIds === []) {
                    return 0;
                }

                return (int) DB::table('sales')
                    ->where('status', $completed)
                    ->whereIn('store_id', $storeIds)
                    ->count();
            },
            'open_shifts' => static function () use ($storeIds, $open): int {
                if ($storeIds === []) {
                    return 0;
                }

                return (int) DB::table('cash_shifts')
                    ->where('status', $open)
                    ->whereIn('store_id', $storeIds)
                    ->count();
            },
        ]);

        return new AdminDashboardMetrics(
            productsTotal: $results['products_total'],
            productsActive: $results['products_active'],
            customersTotal: $results['customers_total'],
            salesCompleted: $results['sales_completed'],
            openShifts: $results['open_shifts'],
        );
    }
}
