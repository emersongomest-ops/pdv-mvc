<?php

declare(strict_types=1);

namespace App\Domain\CashShift\Repositories;

use App\Domain\CashShift\DTOs\ShiftClosingReport;
use App\Models\CashShift;
use Illuminate\Support\Collection;

interface CashShiftRepositoryInterface
{
    public function findOpenForUser(int $userId): ?CashShift;

    public function findOpenForUserAtStore(int $userId, int $storeId): ?CashShift;

    public function findById(int $id): ?CashShift;

    public function createOpen(int $storeId, int $userId, int $openingCashAmountCents): CashShift;

    public function close(CashShift $shift, ?int $closingCashAmountCents): CashShift;

    public function reopen(CashShift $shift): CashShift;

    public function buildClosingReport(CashShift $shift): ShiftClosingReport;

    /**
     * @return Collection<int, CashShift>
     */
    public function listForStore(int $storeId, int $limit = 50): Collection;
}
