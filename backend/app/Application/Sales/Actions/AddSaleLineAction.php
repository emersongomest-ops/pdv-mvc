<?php

declare(strict_types=1);

namespace App\Application\Sales\Actions;

use App\Application\Sales\Support\SaleCartGuard;
use App\Domain\Catalog\Repositories\CatalogRepositoryInterface;
use App\Domain\Sales\Exceptions\SaleDomainException;
use App\Domain\Sales\Repositories\SalesRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\SaleLine;

final class AddSaleLineAction
{
    public function __construct(
        private readonly SalesRepositoryInterface $sales,
        private readonly CatalogRepositoryInterface $catalog,
    ) {}

    public function execute(
        int $saleId,
        int $productId,
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

        $product = $this->catalog->findProductById($productId);

        if ($product === null) {
            throw new SaleDomainException(ErrorCode::CatProductNotFound);
        }

        if (! $product->isActive()) {
            throw new SaleDomainException(ErrorCode::InvProductInactive);
        }

        return $this->sales->addLine($sale, $product->id, (int) $product->base_price, $quantity);
    }
}
