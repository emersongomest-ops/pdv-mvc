<?php

declare(strict_types=1);

namespace App\Application\RefundsReturns\Actions;

use App\Application\Store\Support\AssertManagerStoreAccess;
use App\Domain\RefundsReturns\Repositories\RefundsReturnsRepositoryInterface;
use App\Domain\Sales\Exceptions\SaleDomainException;
use App\Domain\Sales\Repositories\SalesRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Support\Collection;

final class ListRefundsForSaleAction
{
    public function __construct(
        private readonly RefundsReturnsRepositoryInterface $refunds,
        private readonly SalesRepositoryInterface $sales,
        private readonly AssertManagerStoreAccess $storeAccess,
    ) {}

    /**
     * @return Collection<int, Refund>
     */
    public function execute(User $manager, int $saleId): Collection
    {
        $sale = $this->sales->findById($saleId);

        if ($sale === null) {
            throw new SaleDomainException(ErrorCode::SaleNotFound);
        }

        $this->storeAccess->assertCanAccess($manager, (int) $sale->store_id);

        return $this->refunds->listForSale($saleId);
    }
}
