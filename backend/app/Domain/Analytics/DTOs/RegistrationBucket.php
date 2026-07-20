<?php

declare(strict_types=1);

namespace App\Domain\Analytics\DTOs;

final readonly class RegistrationBucket
{
    public function __construct(
        public string $date,
        public int $count,
    ) {}
}
