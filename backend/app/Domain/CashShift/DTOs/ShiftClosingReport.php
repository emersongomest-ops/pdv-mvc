<?php

declare(strict_types=1);

namespace App\Domain\CashShift\DTOs;

/**
 * Closing consolidation for a cash shift (RN-003 / RN-063).
 *
 * Money fields are integer cents.
 */
final readonly class ShiftClosingReport
{
    /**
     * @param list<array{method: string, amount_cents: int}> $totalsByPaymentMethod
     */
    public function __construct(
        public int $shiftId,
        public int $storeId,
        public int $operatorId,
        public int $salesCount,
        public int $salesTotalCents,
        public array $totalsByPaymentMethod,
        public int $openingCashCents,
        public int $expectedCashCents,
        public ?int $closingCashCents,
        public ?int $cashVarianceCents,
        public ?string $openedAt,
        public ?string $closedAt,
        public string $status,
        public ?string $operatorName = null,
        public ?string $storeCode = null,
    ) {}
}
