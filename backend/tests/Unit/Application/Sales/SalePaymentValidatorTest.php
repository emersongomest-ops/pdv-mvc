<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Sales;

use App\Application\Sales\Support\SalePaymentValidator;
use App\Domain\Payments\Exceptions\PaymentDomainException;
use App\Domain\Sales\Exceptions\SaleDomainException;
use App\Domain\Shared\ErrorCode;
use Tests\TestCase;

final class SalePaymentValidatorTest extends TestCase
{
    private SalePaymentValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = $this->app->make(SalePaymentValidator::class);
    }

    public function test_accepts_payment_total_within_tolerance(): void
    {
        $this->validator->assertValidForCompletion([
            ['method' => 'pix', 'amount' => 500],
            ['method' => 'cash', 'amount' => 501, 'cash_received' => 1000],
        ], 1000);

        $this->assertTrue(true);
    }

    public function test_rejects_payment_total_outside_tolerance(): void
    {
        $this->expectException(SaleDomainException::class);
        $this->expectExceptionMessage(ErrorCode::SalePaymentMismatch->message());

        $this->validator->assertValidForCompletion([
            ['method' => 'pix', 'amount' => 500],
            ['method' => 'cash', 'amount' => 502, 'cash_received' => 1000],
        ], 1000);
    }

    public function test_rejects_insufficient_cash_received(): void
    {
        $this->expectException(PaymentDomainException::class);
        $this->expectExceptionMessage(ErrorCode::PayCashInsufficient->message());

        $this->validator->assertValidForCompletion([
            ['method' => 'cash', 'amount' => 1000, 'cash_received' => 999],
        ], 1000);
    }
}
