<?php

declare(strict_types=1);

namespace App\Application\Sales\Actions;

use App\Application\Sales\Support\SaleCartGuard;
use App\Domain\Sales\Exceptions\SaleDomainException;
use App\Domain\Sales\Repositories\SalesRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\SaleLine;

final class UpdateSaleLineAction
{
    public function __construct(
        private readonly SalesRepositoryInterface $sales,
    ) {}

    public function execute(
        int $saleId,
        int $lineId,
        int $quantity,
        int $storeId,
        int $userId,
        int $cashShiftId,
    ): SaleLine {
        $sale = $this->sales->findById($saleId);

        if ($sale === null) {
            throw new SaleDomainException(ErrorCode::SaleNotFound);
        }

        SaleCartGuard::assertMutable($sale, $storeId, $userId, $cashShiftId);

        $line = $this->sales->findLineById($saleId, $lineId);

        if ($line === null) {
            throw new SaleDomainException(ErrorCode::SaleLineNotFound);
        }

        return $this->sales->updateLineQuantity($line, $quantity);
    }
}
