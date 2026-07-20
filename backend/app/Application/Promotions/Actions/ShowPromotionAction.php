<?php

declare(strict_types=1);

namespace App\Application\Promotions\Actions;

use App\Domain\Promotions\Exceptions\PromotionDomainException;
use App\Domain\Promotions\Repositories\PromotionsRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Promotion;

final class ShowPromotionAction
{
    public function __construct(
        private readonly PromotionsRepositoryInterface $promotions,
    ) {}

    public function execute(int $promotionId): Promotion
    {
        $promotion = $this->promotions->findById($promotionId);

        if ($promotion === null) {
            throw new PromotionDomainException(ErrorCode::PromoNotFound);
        }

        return $promotion;
    }
}
