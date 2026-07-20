<?php

declare(strict_types=1);

namespace App\Application\Sales\Actions;

use App\Domain\Sales\Repositories\SalesRepositoryInterface;
use App\Models\Sale;
use Illuminate\Support\Collection;

final class ListHeldSalesAction
{
    public function __construct(
        private readonly SalesRepositoryInterface $sales,
    ) {}

    /**
     * @return Collection<int, Sale>
     */
    public function execute(int $storeId, int $userId, int $cashShiftId): Collection
    {
        return $this->sales->listHeldForShift($storeId, $userId, $cashShiftId);
    }
}
