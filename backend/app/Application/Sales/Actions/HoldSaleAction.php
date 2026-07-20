<?php

declare(strict_types=1);

namespace App\Application\Sales\Actions;

use App\Application\Sales\Support\SaleCartGuard;
use App\Domain\Sales\Exceptions\SaleDomainException;
use App\Domain\Sales\Repositories\SalesRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Sale;

final class HoldSaleAction
{
    public function __construct(
        private readonly SalesRepositoryInterface $sales,
    ) {}

    public function execute(
        int $saleId,
        ?string $label,
        int $storeId,
        int $userId,
        int $cashShiftId,
    ): Sale {
        $sale = $this->sales->findById($saleId);

        if ($sale === null) {
            throw new SaleDomainException(ErrorCode::SaleNotFound);
        }

        SaleCartGuard::assertHoldable($sale, $storeId, $userId, $cashShiftId);

        return $this->sales->hold($sale, $label);
    }
}
