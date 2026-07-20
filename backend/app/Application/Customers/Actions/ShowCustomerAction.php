<?php

declare(strict_types=1);

namespace App\Application\Customers\Actions;

use App\Domain\Customers\Exceptions\CustomerDomainException;
use App\Domain\Customers\Repositories\CustomersRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Customer;

final class ShowCustomerAction
{
    public function __construct(
        private readonly CustomersRepositoryInterface $customers,
    ) {}

    public function execute(int $customerId): Customer
    {
        $customer = $this->customers->findById($customerId);

        if ($customer === null) {
            throw new CustomerDomainException(ErrorCode::CustNotFound);
        }

        return $customer;
    }
}
