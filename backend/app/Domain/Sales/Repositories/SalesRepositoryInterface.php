<?php

declare(strict_types=1);

namespace App\Domain\Sales\Repositories;

use App\Domain\Sales\DTOs\AdminSaleFilters;
use App\Models\Sale;
use App\Models\SaleLine;
use Illuminate\Support\Collection;

interface SalesRepositoryInterface
{
    public function findById(int $id): ?Sale;

    public function createInProgress(int $storeId, int $userId, int $cashShiftId): Sale;

    /**
     * Manager sales listing with optional filters (RN-061 / RN-064).
     *
     * @param list<int> $allowedStoreIds
     * @return Collection<int, Sale>
     */
    public function listForAdmin(AdminSaleFilters $filters, array $allowedStoreIds): Collection;

    /**
     * @return Collection<int, Sale>
     */
    public function listHeldForShift(int $storeId, int $userId, int $cashShiftId): Collection;

    public function hold(Sale $sale, ?string $label): Sale;

    public function resume(Sale $sale): Sale;

    public function findLineById(int $saleId, int $lineId): ?SaleLine;

    public function findLineByProduct(int $saleId, int $productId): ?SaleLine;

    public function addLine(Sale $sale, int $productId, int $unitPriceCents, int $quantity): SaleLine;

    public function updateLineQuantity(SaleLine $line, int $quantity): SaleLine;

    public function removeLine(SaleLine $line): void;

    public function recalculateTotals(Sale $sale): Sale;

    public function attachCustomer(Sale $sale, int $customerId): Sale;

    public function complete(Sale $sale): Sale;
}
