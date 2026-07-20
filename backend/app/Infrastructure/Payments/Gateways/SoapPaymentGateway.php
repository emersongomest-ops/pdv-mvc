<?php

declare(strict_types=1);

namespace App\Infrastructure\Payments\Gateways;

use App\Domain\Payments\DTOs\PaymentRequest;
use App\Domain\Payments\DTOs\PaymentResult;
use App\Domain\Payments\DTOs\RefundRequest;
use App\Domain\Payments\DTOs\RefundResult;
use App\Domain\Payments\Exceptions\PaymentDomainException;
use App\Domain\Payments\Gateways\PaymentGatewayInterface;
use App\Domain\Payments\ValueObjects\PaymentChargeStatus;
use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Domain\Shared\ErrorCode;
use App\Domain\Shared\Money;
use App\Infrastructure\Payments\Soap\PaymentSoapEnvelopeBuilder;
use SoapClient;
use SoapFault;

/**
 * Outbound acquirer transport over SOAP. REST remains for the PDV API + webhooks.
 */
final class SoapPaymentGateway implements PaymentGatewayInterface
{
    public function __construct(
        private readonly PaymentSoapEnvelopeBuilder $envelopes,
    ) {}

    public function charge(PaymentRequest $request): PaymentResult
    {
        $namespace = (string) config('payments.soap.namespace');
        $envelope = $this->envelopes->chargeEnvelope($request, $namespace);
        $this->envelopes->assertWellFormed($envelope);

        if ($this->isStubMode()) {
            $changeAmount = null;
            if ($request->method === PaymentMethod::Cash && $request->cashReceived !== null) {
                $changeAmount = Money::sub($request->cashReceived, $request->amount);
            }

            return new PaymentResult(
                success: true,
                transactionReference: 'soap-stub-'.bin2hex(random_bytes(8)),
                changeAmount: $changeAmount,
                awaitingConfirmation: $this->awaitsConfirmation($request->method),
            );
        }

        try {
            $client = $this->makeClient();
            $client->__doRequest(
                $envelope,
                (string) config('payments.soap.endpoint'),
                'Charge',
                SOAP_1_1,
            );
        } catch (SoapFault) {
            throw new PaymentDomainException(ErrorCode::PayGatewayUnavailable);
        }

        return new PaymentResult(
            success: true,
            transactionReference: 'soap-'.bin2hex(random_bytes(8)),
            changeAmount: $request->method === PaymentMethod::Cash && $request->cashReceived !== null
                ? Money::sub($request->cashReceived, $request->amount)
                : null,
            awaitingConfirmation: $this->awaitsConfirmation($request->method),
        );
    }

    public function queryChargeStatus(string $transactionReference): PaymentChargeStatus
    {
        if ($this->isStubMode()) {
            // Stub: settle as confirmed so hourly reconcile / force-refresh closes the outbox.
            return PaymentChargeStatus::Confirmed;
        }

        try {
            $client = $this->makeClient();
            $namespace = (string) config('payments.soap.namespace');
            $envelope = $this->envelopes->statusEnvelope($transactionReference, $namespace);
            $this->envelopes->assertWellFormed($envelope);
            $client->__doRequest(
                $envelope,
                (string) config('payments.soap.endpoint'),
                'QueryStatus',
                SOAP_1_1,
            );
        } catch (SoapFault) {
            throw new PaymentDomainException(ErrorCode::PayGatewayUnavailable);
        }

        return PaymentChargeStatus::Confirmed;
    }

    private function awaitsConfirmation(PaymentMethod $method): bool
    {
        if (! (bool) config('payments.async_settlement.enabled', true)) {
            return false;
        }

        return $method !== PaymentMethod::Cash;
    }

    public function refund(RefundRequest $request): RefundResult
    {
        $namespace = (string) config('payments.soap.namespace');
        $envelope = $this->envelopes->refundEnvelope($request, $namespace);
        $this->envelopes->assertWellFormed($envelope);

        if ($this->isStubMode()) {
            return new RefundResult(
                success: true,
                refundReference: 'soap-stub-refund-'.bin2hex(random_bytes(8)),
            );
        }

        try {
            $client = $this->makeClient();
            $client->__doRequest(
                $envelope,
                (string) config('payments.soap.endpoint'),
                'Refund',
                SOAP_1_1,
            );
        } catch (SoapFault) {
            throw new PaymentDomainException(ErrorCode::PayGatewayUnavailable);
        }

        return new RefundResult(
            success: true,
            refundReference: 'soap-refund-'.bin2hex(random_bytes(8)),
        );
    }

    private function isStubMode(): bool
    {
        return (string) config('payments.soap.mode', 'stub') !== 'live';
    }

    private function makeClient(): SoapClient
    {
        $wsdl = config('payments.soap.wsdl');
        if (! is_string($wsdl) || $wsdl === '') {
            throw new PaymentDomainException(ErrorCode::PayGatewayUnavailable);
        }

        return new SoapClient($wsdl, [
            'trace' => false,
            'exceptions' => true,
            'connection_timeout' => (int) config('payments.soap.timeout_seconds', 30),
            'cache_wsdl' => WSDL_CACHE_MEMORY,
        ]);
    }
}
