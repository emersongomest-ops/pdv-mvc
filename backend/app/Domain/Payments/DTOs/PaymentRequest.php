<?php

declare(strict_types=1);

namespace App\Domain\Payments\DTOs;

use App\Domain\Payments\ValueObjects\PaymentMethod;

final readonly class PaymentRequest
{
    public function __construct(
        public PaymentMethod $method,
        public int $amount,
        public ?int $cashReceived = null,
    ) {}
}
