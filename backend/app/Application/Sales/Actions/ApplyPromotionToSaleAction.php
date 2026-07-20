<?php

declare(strict_types=1);

namespace App\Application\Sales\Actions;

use App\Application\Sales\Support\SaleCartGuard;
use App\Domain\Promotions\Exceptions\PromotionDomainException;
use App\Domain\Promotions\Repositories\PromotionsRepositoryInterface;
use App\Domain\Promotions\Support\PromotionDiscountCalculator;
use App\Domain\Promotions\ValueObjects\StackingMode;
use App\Domain\Sales\Exceptions\SaleDomainException;
use App\Domain\Sales\Repositories\SalesRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\SalePromotion;

final class ApplyPromotionToSaleAction
{
    public function __construct(
        private readonly SalesRepositoryInterface $sales,
        private readonly PromotionsRepositoryInterface $promotions,
    ) {}

    public function execute(
        int $saleId,
        string $code,
        int $storeId,
        int $userId,
        int $cashShiftId,
    ): Sale {
        $sale = $this->sales->findById($saleId);

        if ($sale === null) {
            throw new SaleDomainException(ErrorCode::SaleNotFound);
        }

        SaleCartGuard::assertMutable($sale, $storeId, $userId, $cashShiftId);

        $promotion = $this->promotions->findByCode($code);

        if ($promotion === null) {
            throw new PromotionDomainException(ErrorCode::PromoNotFound);
        }

        $this->assertPromotionUsable($promotion, $sale);

        if ($this->promotions->findApplied($sale->id, $promotion->id) !== null) {
            throw new PromotionDomainException(ErrorCode::PromoNotCombinable);
        }

        $applied = $this->promotions->listAppliedForSale($sale->id);
        $this->assertStackingAllowed($promotion, $applied);

        $amount = PromotionDiscountCalculator::amountFor((int) $sale->subtotal, $promotion);
        $this->promotions->attachToSale($sale->id, $promotion->id, $amount);

        return $this->sales->recalculateTotals($sale);
    }

    private function assertPromotionUsable(Promotion $promotion, Sale $sale): void
    {
        if (! $promotion->is_active || ! $promotion->isStarted()) {
            throw new PromotionDomainException(ErrorCode::PromoNotApplicable);
        }

        if ($promotion->isExpired()) {
            throw new PromotionDomainException(ErrorCode::PromoExpired);
        }

        if ($promotion->applies_to_all_customers) {
            return;
        }

        if ($sale->customer_id === null) {
            throw new PromotionDomainException(ErrorCode::PromoNotApplicable);
        }

        if (! $this->promotions->isAssignedToCustomer($promotion->id, $sale->customer_id)) {
            throw new PromotionDomainException(ErrorCode::PromoNotAssigned);
        }
    }

    /**
     * @param \Illuminate\Support\Collection<int, SalePromotion> $applied
     */
    private function assertStackingAllowed(Promotion $incoming, $applied): void
    {
        if ($applied->isEmpty()) {
            return;
        }

        if ($incoming->stacking_mode === StackingMode::Unique) {
            throw new PromotionDomainException(ErrorCode::PromoNotCombinable);
        }

        foreach ($applied as $row) {
            $existing = $row->promotion;

            if ($existing === null) {
                continue;
            }

            if ($existing->stacking_mode === StackingMode::Unique) {
                throw new PromotionDomainException(ErrorCode::PromoNotCombinable);
            }
        }
    }
}
