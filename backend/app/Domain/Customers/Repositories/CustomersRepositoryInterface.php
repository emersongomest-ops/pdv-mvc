<?php

declare(strict_types=1);

namespace App\Domain\Customers\Repositories;

use App\Models\Customer;
use Illuminate\Support\Collection;

interface CustomersRepositoryInterface
{
    /**
     * @return Collection<int, Customer>
     */
    public function list(?string $search = null): Collection;

    /**
     * Keyset page ordered by name, id.
     *
     * @return array{items: Collection<int, Customer>, next_cursor: string|null}
     */
    public function listPage(?string $search, ?string $cursor, int $perPage): array;

    public function findById(int $id): ?Customer;

    public function findByCpf(string $cpf): ?Customer;

    public function cpfExists(string $cpf, ?int $exceptCustomerId = null): bool;

    /**
     * @param array{
     *     name: string,
     *     email: string,
     *     cpf: string,
     *     phone: string,
     *     birth_date: string,
     *     address: string
     * } $data
     */
    public function create(array $data): Customer;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Customer $customer, array $data): Customer;

    public function recordCompletedPurchase(int $customerId, int $storeId, int $amountCents): void;
}
