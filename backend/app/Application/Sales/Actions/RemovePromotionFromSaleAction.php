<?php

declare(strict_types=1);

namespace App\Application\Sales\Actions;

use App\Application\Sales\Support\SaleCartGuard;
use App\Domain\Promotions\Exceptions\PromotionDomainException;
use App\Domain\Promotions\Repositories\PromotionsRepositoryInterface;
use App\Domain\Sales\Exceptions\SaleDomainException;
use App\Domain\Sales\Repositories\SalesRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Sale;

final class RemovePromotionFromSaleAction
{
    public function __construct(
        private readonly SalesRepositoryInterface $sales,
        private readonly PromotionsRepositoryInterface $promotions,
    ) {}

    public function execute(
        int $saleId,
        int $promotionId,
        int $storeId,
        int $userId,
        int $cashShiftId,
    ): Sale {
        $sale = $this->sales->findById($saleId);

        if ($sale === null) {
            throw new SaleDomainException(ErrorCode::SaleNotFound);
        }

        SaleCartGuard::assertMutable($sale, $storeId, $userId, $cashShiftId);

        $applied = $this->promotions->findApplied($saleId, $promotionId);

        if ($applied === null) {
            throw new PromotionDomainException(ErrorCode::PromoNotFound);
        }

        $this->promotions->detachFromSale($saleId, $promotionId);

        return $this->sales->recalculateTotals($sale);
    }
}
