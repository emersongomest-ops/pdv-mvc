<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Payments;

use App\Domain\Payments\Card\CardInstrumentFormatGuard;
use App\Domain\Payments\DTOs\CardInstrument;
use App\Domain\Payments\Exceptions\PaymentDomainException;
use App\Domain\Shared\ErrorCode;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CardInstrumentFormatGuardTest extends TestCase
{
    private CardInstrumentFormatGuard $guard;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guard = new CardInstrumentFormatGuard;
    }

    #[Test]
    public function accepts_well_formed_card_matching_indicated_person(): void
    {
        $this->guard->assertWellFormed($this->validCard());
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function rejects_invalid_luhn_number(): void
    {
        $this->expectExceptionObject(new PaymentDomainException(ErrorCode::PayCardNumberInvalid));

        $this->guard->assertWellFormed($this->validCard(number: '4111111111111112'));
    }

    #[Test]
    public function rejects_holder_mismatch(): void
    {
        $this->expectExceptionObject(new PaymentDomainException(ErrorCode::PayCardHolderMismatch));

        $this->guard->assertWellFormed($this->validCard(
            holderName: 'Maria Silva',
            indicatedPersonName: 'Joao Santos',
        ));
    }

    #[Test]
    public function rejects_unconfirmed_ownership(): void
    {
        $this->expectExceptionObject(new PaymentDomainException(ErrorCode::PayCardOwnershipUnconfirmed));

        $this->guard->assertWellFormed($this->validCard(belongsToIndicatedPerson: false));
    }

    #[Test]
    public function rejects_expired_card(): void
    {
        $this->expectExceptionObject(new PaymentDomainException(ErrorCode::PayCardExpiryInvalid));

        $this->guard->assertWellFormed($this->validCard(expiryMonth: 1, expiryYear: 2020));
    }

    private function validCard(
        string $holderName = 'Maria Silva',
        string $number = '4111111111111111',
        int $expiryMonth = 12,
        int $expiryYear = 2030,
        string $indicatedPersonName = 'Maria Silva',
        bool $belongsToIndicatedPerson = true,
    ): CardInstrument {
        return new CardInstrument(
            holderName: $holderName,
            number: $number,
            expiryMonth: $expiryMonth,
            expiryYear: $expiryYear,
            indicatedPersonName: $indicatedPersonName,
            belongsToIndicatedPerson: $belongsToIndicatedPerson,
        );
    }
}
