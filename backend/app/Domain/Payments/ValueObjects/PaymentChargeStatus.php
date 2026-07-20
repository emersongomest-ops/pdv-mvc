<?php

declare(strict_types=1);

namespace App\Domain\Payments\ValueObjects;

enum PaymentChargeStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Failed = 'failed';
}
