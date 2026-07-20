<?php

declare(strict_types=1);

namespace App\Domain\Payments\DTOs;

final readonly class PaymentResult
{
    public function __construct(
        public bool $success,
        public string $transactionReference,
        public ?int $changeAmount = null,
        /** When true, line stays pending until webhook or reconcile poll. */
        public bool $awaitingConfirmation = false,
    ) {}
}
