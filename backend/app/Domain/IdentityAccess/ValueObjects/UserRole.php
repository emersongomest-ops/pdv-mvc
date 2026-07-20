<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\ValueObjects;

enum UserRole: string
{
    case Manager = 'manager';
    case Operator = 'operator';

    public function label(): string
    {
        return match ($this) {
            self::Manager => 'Manager',
            self::Operator => 'Operator',
        };
    }
}
