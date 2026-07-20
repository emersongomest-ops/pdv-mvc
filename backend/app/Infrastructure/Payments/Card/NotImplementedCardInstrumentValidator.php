<?php

declare(strict_types=1);

namespace App\Infrastructure\Payments\Card;

use App\Domain\Payments\Card\CardInstrumentFormatGuard;
use App\Domain\Payments\Card\CardInstrumentValidatorInterface;
use App\Domain\Payments\DTOs\CardInstrument;
use App\Domain\Payments\Exceptions\PaymentDomainException;
use App\Domain\Shared\ErrorCode;

/**
 * Bound until a live acquirer adapter exists: format/ownership locally, then refuse charge.
 */
final class NotImplementedCardInstrumentValidator implements CardInstrumentValidatorInterface
{
    public function __construct(
        private readonly CardInstrumentFormatGuard $formatGuard,
    ) {}

    public function assertValidForCharge(CardInstrument $card): void
    {
        $this->formatGuard->assertWellFormed($card);

        throw new PaymentDomainException(ErrorCode::PayMethodNotImplemented);
    }
}
