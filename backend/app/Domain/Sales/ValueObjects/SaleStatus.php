<?php

declare(strict_types=1);

namespace App\Domain\Sales\ValueObjects;

enum SaleStatus: string
{
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Held = 'held';
}
