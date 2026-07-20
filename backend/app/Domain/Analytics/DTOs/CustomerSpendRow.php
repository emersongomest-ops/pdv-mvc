<?php

declare(strict_types=1);

namespace App\Domain\Analytics\DTOs;

/**
 * @param  list<array{store_id: int, store_code: string|null, purchase_count: int, total_spend_cents: int}>  $storeSpend
 */
final readonly class CustomerSpendRow
{
    /**
     * @param  list<array{store_id: int, store_code: string|null, purchase_count: int, total_spend_cents: int}>  $storeSpend
     */
    public function __construct(
        public int $customerId,
        public string $name,
        public string $cpf,
        public int $lifetimeSpendCents,
        public array $storeSpend,
    ) {}
}
