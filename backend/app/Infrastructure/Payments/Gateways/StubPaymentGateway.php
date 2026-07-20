<?php

declare(strict_types=1);

namespace App\Infrastructure\Payments\Gateways;

use App\Domain\Payments\DTOs\PaymentRequest;
use App\Domain\Payments\DTOs\PaymentResult;
use App\Domain\Payments\DTOs\RefundRequest;
use App\Domain\Payments\DTOs\RefundResult;
use App\Domain\Payments\Gateways\PaymentGatewayInterface;
use App\Domain\Payments\ValueObjects\PaymentChargeStatus;
use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Domain\Shared\Money;

final class StubPaymentGateway implements PaymentGatewayInterface
{
    public function charge(PaymentRequest $request): PaymentResult
    {
        $changeAmount = null;

        if ($request->method === PaymentMethod::Cash && $request->cashReceived !== null) {
            $changeAmount = Money::sub($request->cashReceived, $request->amount);
        }

        return new PaymentResult(
            success: true,
            transactionReference: 'stub-'.bin2hex(random_bytes(8)),
            changeAmount: $changeAmount,
            awaitingConfirmation: $request->method !== PaymentMethod::Cash
                && (bool) config('payments.async_settlement.enabled', true),
        );
    }

    public function refund(RefundRequest $request): RefundResult
    {
        return new RefundResult(
            success: true,
            refundReference: 'stub-refund-'.bin2hex(random_bytes(8)),
        );
    }

    public function queryChargeStatus(string $transactionReference): PaymentChargeStatus
    {
        return PaymentChargeStatus::Confirmed;
    }
}
