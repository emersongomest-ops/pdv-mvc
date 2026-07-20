<?php

declare(strict_types=1);

namespace App\Domain\Payments\DTOs;

final readonly class RefundRequest
{
    public function __construct(
        public string $transactionReference,
        public int $amount,
    ) {}
}
