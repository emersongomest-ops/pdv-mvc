<?php

declare(strict_types=1);

namespace App\Domain\Payments\ValueObjects;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Pix = 'pix';
    case DebitCard = 'debit_card';
    case CreditCard = 'credit_card';
    case Voucher = 'voucher';
    case StoreCredit = 'store_credit';
    case Other = 'other';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $method): string => $method->value,
            self::cases(),
        );
    }
}
