<?php

declare(strict_types=1);

namespace App\Application\Customers\Actions;

use App\Application\Customers\Support\CustomerPayloadNormalizer;
use App\Domain\Customers\Exceptions\CustomerDomainException;
use App\Domain\Customers\Repositories\CustomersRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Customer;

final class FindCustomerByCpfAction
{
    public function __construct(
        private readonly CustomersRepositoryInterface $customers,
    ) {}

    public function execute(string $cpf): Customer
    {
        $normalized = CustomerPayloadNormalizer::digitsOnly($cpf);

        if ($normalized === '') {
            throw new CustomerDomainException(ErrorCode::CustRequiredFieldMissing);
        }

        $customer = $this->customers->findByCpf($normalized);

        if ($customer === null) {
            throw new CustomerDomainException(ErrorCode::CustNotFound);
        }

        return $customer;
    }
}
