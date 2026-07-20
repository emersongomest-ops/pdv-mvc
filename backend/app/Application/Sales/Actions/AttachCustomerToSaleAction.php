<?php

declare(strict_types=1);

namespace App\Application\Sales\Actions;

use App\Application\Sales\Support\SaleCartGuard;
use App\Domain\Customers\Exceptions\CustomerDomainException;
use App\Domain\Customers\Repositories\CustomersRepositoryInterface;
use App\Domain\Sales\Exceptions\SaleDomainException;
use App\Domain\Sales\Repositories\SalesRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Sale;

final class AttachCustomerToSaleAction
{
    public function __construct(
        private readonly SalesRepositoryInterface $sales,
        private readonly CustomersRepositoryInterface $customers,
    ) {}

    public function execute(
        int $saleId,
        int $customerId,
        int $storeId,
        int $userId,
        int $cashShiftId,
    ): Sale {
        $sale = $this->sales->findById($saleId);

        if ($sale === null) {
            throw new SaleDomainException(ErrorCode::SaleNotFound);
        }

        SaleCartGuard::assertMutable($sale, $storeId, $userId, $cashShiftId);

        $customer = $this->customers->findById($customerId);

        if ($customer === null) {
            throw new CustomerDomainException(ErrorCode::CustNotFound);
        }

        return $this->sales->attachCustomer($sale, $customerId);
    }
}
