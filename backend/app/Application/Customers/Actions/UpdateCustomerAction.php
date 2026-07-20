<?php

declare(strict_types=1);

namespace App\Application\Customers\Actions;

use App\Application\Customers\Support\CustomerPayloadNormalizer;
use App\Domain\Customers\Exceptions\CustomerDomainException;
use App\Domain\Customers\Repositories\CustomersRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Customer;

final class UpdateCustomerAction
{
    public function __construct(
        private readonly CustomersRepositoryInterface $customers,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function execute(int $customerId, array $data): Customer
    {
        $customer = $this->customers->findById($customerId);

        if ($customer === null) {
            throw new CustomerDomainException(ErrorCode::CustNotFound);
        }

        $updates = [];

        foreach (['name', 'email', 'phone', 'birth_date', 'address'] as $field) {
            if (array_key_exists($field, $data)) {
                $value = is_string($data[$field]) ? trim($data[$field]) : (string) $data[$field];

                if ($value === '') {
                    throw new CustomerDomainException(ErrorCode::CustRequiredFieldMissing);
                }

                $updates[$field] = $value;
            }
        }

        if (array_key_exists('cpf', $data)) {
            $cpf = CustomerPayloadNormalizer::digitsOnly((string) $data['cpf']);

            if ($cpf === '') {
                throw new CustomerDomainException(ErrorCode::CustRequiredFieldMissing);
            }

            if ($this->customers->cpfExists($cpf, $customerId)) {
                throw new CustomerDomainException(ErrorCode::CustCpfDuplicate);
            }

            $updates['cpf'] = $cpf;
        }

        if ($updates === []) {
            return $customer;
        }

        return $this->customers->update($customer, $updates);
    }
}
