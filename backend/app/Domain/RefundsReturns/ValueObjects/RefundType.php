<?php

declare(strict_types=1);

namespace App\Domain\RefundsReturns\ValueObjects;

enum RefundType: string
{
    case FullRefund = 'full_refund';
    case PartialRefund = 'partial_refund';
    case FullReturn = 'full_return';
    case PartialReturn = 'partial_return';

    public function restocks(): bool
    {
        return match ($this) {
            self::FullRefund, self::FullReturn, self::PartialReturn => true,
            self::PartialRefund => false,
        };
    }

    public function isFull(): bool
    {
        return $this === self::FullRefund || $this === self::FullReturn;
    }
}
