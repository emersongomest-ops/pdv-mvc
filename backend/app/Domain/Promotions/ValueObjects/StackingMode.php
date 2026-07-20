<?php

declare(strict_types=1);

namespace App\Domain\Promotions\ValueObjects;

enum StackingMode: string
{
    case Unique = 'unique';
    case Accumulable = 'accumulable';
}
