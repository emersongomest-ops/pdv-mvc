<?php

declare(strict_types=1);

namespace App\Application\Customers\Actions;

use App\Application\Customers\Support\CustomerPayloadNormalizer;
use App\Domain\Customers\Exceptions\CustomerDomainException;
use App\Domain\Customers\Repositories\CustomersRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Customer;

final class CreateCustomerAction
{
    public function __construct(
        private readonly CustomersRepositoryInterface $customers,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function execute(array $data): Customer
    {
        $payload = CustomerPayloadNormalizer::normalizeCreate($data);
        CustomerPayloadNormalizer::assertRequiredFieldsPresent($payload);

        if ($this->customers->cpfExists($payload['cpf'])) {
            throw new CustomerDomainException(ErrorCode::CustCpfDuplicate);
        }

        return $this->customers->create($payload);
    }
}
