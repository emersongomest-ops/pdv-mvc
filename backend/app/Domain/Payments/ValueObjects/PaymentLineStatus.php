<?php

declare(strict_types=1);

namespace App\Domain\Payments\ValueObjects;

enum PaymentLineStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Failed = 'failed';
}
