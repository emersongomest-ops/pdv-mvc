<?php

declare(strict_types=1);

namespace App\Application\Customers\Actions;

use App\Domain\Customers\Repositories\CustomersRepositoryInterface;
use App\Models\Customer;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class ListCustomersAction
{
    public function __construct(
        private readonly CustomersRepositoryInterface $customers,
    ) {}

    /**
     * Without per_page: full list (promotion pickers). With per_page: keyset page.
     *
     * @return array{customers: Collection<int, Customer>, next_cursor: string|null}
     */
    public function execute(
        ?string $search = null,
        ?string $cursor = null,
        ?int $perPage = null,
    ): array {
        if ($perPage === null) {
            return [
                'customers' => $this->customers->list($search),
                'next_cursor' => null,
            ];
        }

        try {
            $page = $this->customers->listPage($search, $cursor, $perPage);
        } catch (InvalidArgumentException $e) {
            throw $e;
        }

        return [
            'customers' => $page['items'],
            'next_cursor' => $page['next_cursor'],
        ];
    }
}
