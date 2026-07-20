<?php

declare(strict_types=1);

namespace App\Domain\Payments\Card;

use App\Domain\Payments\DTOs\CardInstrument;

/**
 * Acquirer / issuer-side card checks (BIN, AVS, 3DS, name-on-file).
 * Swap the bound implementation when a live gateway is connected.
 */
interface CardInstrumentValidatorInterface
{
    /**
     * @throws \App\Domain\Payments\Exceptions\PaymentDomainException
     */
    public function assertValidForCharge(CardInstrument $card): void;
}
