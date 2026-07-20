<?php

declare(strict_types=1);

namespace App\Domain\Payments\DTOs;

final readonly class RefundResult
{
    public function __construct(
        public bool $success,
        public string $refundReference,
    ) {}
}
