<?php

declare(strict_types=1);

namespace App\Domain\Sales\DTOs;

/**
 * Query filters for manager sales listing (RN-061).
 */
final readonly class AdminSaleFilters
{
    public function __construct(
        public ?string $fromDate = null,
        public ?string $toDate = null,
        public ?int $storeId = null,
        public ?int $operatorId = null,
        public ?int $customerId = null,
        public ?string $paymentMethod = null,
        public ?string $status = 'completed',
    ) {}
}
