<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Payments;

use App\Domain\Payments\DTOs\PaymentRequest;
use App\Domain\Payments\DTOs\RefundRequest;
use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Infrastructure\Payments\Soap\PaymentSoapEnvelopeBuilder;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PaymentSoapEnvelopeBuilderTest extends TestCase
{
    #[Test]
    public function builds_well_formed_charge_and_refund_envelopes(): void
    {
        $builder = new PaymentSoapEnvelopeBuilder;
        $ns = 'urn:pdv:payments';

        $charge = $builder->chargeEnvelope(
            new PaymentRequest(PaymentMethod::Cash, 1500, 2000),
            $ns,
        );
        $builder->assertWellFormed($charge);
        $this->assertStringContainsString('<amountCents>1500</amountCents>', $charge);
        $this->assertStringContainsString('<method>cash</method>', $charge);
        $this->assertStringContainsString('soap:Envelope', $charge);

        $refund = $builder->refundEnvelope(
            new RefundRequest('soap-ref-1', 500),
            $ns,
        );
        $builder->assertWellFormed($refund);
        $this->assertStringContainsString('<transactionReference>soap-ref-1</transactionReference>', $refund);
    }
}
