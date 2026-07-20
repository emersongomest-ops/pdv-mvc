<?php

declare(strict_types=1);

namespace App\Domain\CashShift\ValueObjects;

enum CashShiftStatus: string
{
    case Open = 'open';
    case Closed = 'closed';
}
