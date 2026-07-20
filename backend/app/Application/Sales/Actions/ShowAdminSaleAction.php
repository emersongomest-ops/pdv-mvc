<?php

declare(strict_types=1);

namespace App\Application\Sales\Actions;

use App\Application\Store\Support\AssertManagerStoreAccess;
use App\Domain\Sales\Exceptions\SaleDomainException;
use App\Domain\Sales\Repositories\SalesRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Sale;
use App\Models\User;

/**
 * Manager sale detail for refunds / inspection (no operational shift guard).
 */
final class ShowAdminSaleAction
{
    public function __construct(
        private readonly SalesRepositoryInterface $sales,
        private readonly AssertManagerStoreAccess $storeAccess,
    ) {}

    public function execute(User $manager, int $saleId): Sale
    {
        $sale = $this->sales->findById($saleId);

        if ($sale === null) {
            throw new SaleDomainException(ErrorCode::SaleNotFound);
        }

        $this->storeAccess->assertCanAccess($manager, (int) $sale->store_id);

        return $sale;
    }
}
