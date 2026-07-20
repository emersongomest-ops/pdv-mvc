<?php

declare(strict_types=1);

namespace App\Domain\Payments\Card;

use App\Domain\Payments\DTOs\CardInstrument;
use App\Domain\Payments\Exceptions\PaymentDomainException;
use App\Domain\Shared\ErrorCode;

/**
 * Local, gateway-agnostic checks: PAN shape/Luhn, expiry, holder vs indicated person.
 */
final class CardInstrumentFormatGuard
{
    public function assertWellFormed(CardInstrument $card): void
    {
        if ($card->normalizedHolderName() === '') {
            throw new PaymentDomainException(ErrorCode::PayCardHolderNameInvalid);
        }

        $digits = $card->digitsOnlyNumber();

        if (strlen($digits) < 13 || strlen($digits) > 19 || ! $this->passesLuhn($digits)) {
            throw new PaymentDomainException(ErrorCode::PayCardNumberInvalid);
        }

        if ($card->expiryMonth < 1 || $card->expiryMonth > 12) {
            throw new PaymentDomainException(ErrorCode::PayCardExpiryInvalid);
        }

        $year = $card->expiryYear;
        if ($year < 100) {
            $year += 2000;
        }

        $expiryEnd = \DateTimeImmutable::createFromFormat('!Y-n-j', sprintf('%d-%d-1', $year, $card->expiryMonth));
        if ($expiryEnd === false) {
            throw new PaymentDomainException(ErrorCode::PayCardExpiryInvalid);
        }

        $expiryEnd = $expiryEnd->modify('last day of this month')->setTime(23, 59, 59);
        if ($expiryEnd < new \DateTimeImmutable('now')) {
            throw new PaymentDomainException(ErrorCode::PayCardExpiryInvalid);
        }

        if (! $card->belongsToIndicatedPerson) {
            throw new PaymentDomainException(ErrorCode::PayCardOwnershipUnconfirmed);
        }

        if ($card->normalizedIndicatedPersonName() === '') {
            throw new PaymentDomainException(ErrorCode::PayCardHolderMismatch);
        }

        if ($card->normalizedHolderName() !== $card->normalizedIndicatedPersonName()) {
            throw new PaymentDomainException(ErrorCode::PayCardHolderMismatch);
        }
    }

    private function passesLuhn(string $digits): bool
    {
        $sum = 0;
        $alt = false;

        for ($i = strlen($digits) - 1; $i >= 0; $i--) {
            $n = (int) $digits[$i];
            if ($alt) {
                $n *= 2;
                if ($n > 9) {
                    $n -= 9;
                }
            }
            $sum += $n;
            $alt = ! $alt;
        }

        return $sum % 10 === 0;
    }
}
