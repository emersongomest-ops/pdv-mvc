<?php

declare(strict_types=1);

namespace App\Application\Sales\Support;

use App\Domain\Payments\Card\CardInstrumentValidatorInterface;
use App\Domain\Payments\DTOs\CardInstrument;
use App\Domain\Payments\Exceptions\PaymentDomainException;
use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Domain\Sales\Exceptions\SaleDomainException;
use App\Domain\Shared\ErrorCode;
use App\Domain\Shared\Money;

final class SalePaymentValidator
{
    private const TOLERANCE_CENTS = 1;

    public function __construct(
        private readonly CardInstrumentValidatorInterface $cardValidator,
    ) {}

    /**
     * @param list<array{
     *     method: string,
     *     amount: int,
     *     cash_received?: int|null,
     *     card?: array{
     *         holder_name: string,
     *         number: string,
     *         exp_month: int,
     *         exp_year: int,
     *         indicated_person_name: string,
     *         belongs_to_indicated_person: bool
     *     }
     * }> $payments
     */
    public function assertValidForCompletion(array $payments, int $saleTotalCents): void
    {
        if ($payments === []) {
            throw new SaleDomainException(ErrorCode::SaleNoPayment);
        }

        if ($saleTotalCents < 0) {
            throw new SaleDomainException(ErrorCode::SaleNegativeTotal);
        }

        $sum = 0;

        foreach ($payments as $payment) {
            $method = PaymentMethod::tryFrom($payment['method']);

            if ($method === null) {
                throw new PaymentDomainException(ErrorCode::PayMethodUnsupported);
            }

            $amount = (int) $payment['amount'];
            $sum = Money::add($sum, $amount);

            if ($method === PaymentMethod::Cash) {
                $cashReceived = $payment['cash_received'] ?? null;

                if ($cashReceived === null || (int) $cashReceived < $amount) {
                    throw new PaymentDomainException(ErrorCode::PayCashInsufficient);
                }
            }

            if ($method === PaymentMethod::DebitCard || $method === PaymentMethod::CreditCard) {
                $cardPayload = $payment['card'] ?? null;

                if (! is_array($cardPayload)) {
                    throw new PaymentDomainException(ErrorCode::PayCardHolderNameInvalid);
                }

                $card = new CardInstrument(
                    holderName: (string) ($cardPayload['holder_name'] ?? ''),
                    number: (string) ($cardPayload['number'] ?? ''),
                    expiryMonth: (int) ($cardPayload['exp_month'] ?? 0),
                    expiryYear: (int) ($cardPayload['exp_year'] ?? 0),
                    indicatedPersonName: (string) ($cardPayload['indicated_person_name'] ?? ''),
                    belongsToIndicatedPerson: (bool) ($cardPayload['belongs_to_indicated_person'] ?? false),
                );

                $this->cardValidator->assertValidForCharge($card);
            }
        }

        $absoluteDifference = abs(Money::sub($sum, $saleTotalCents));

        if ($absoluteDifference > self::TOLERANCE_CENTS) {
            throw new SaleDomainException(ErrorCode::SalePaymentMismatch);
        }
    }
}
