<?php

declare(strict_types=1);

namespace App\Domain\Promotions\ValueObjects;

enum DiscountType: string
{
    case Percent = 'percent';
    case Fixed = 'fixed';
}
