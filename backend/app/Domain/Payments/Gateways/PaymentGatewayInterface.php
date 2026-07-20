<?php

declare(strict_types=1);

namespace App\Domain\Payments\Gateways;

use App\Domain\Payments\DTOs\PaymentRequest;
use App\Domain\Payments\DTOs\PaymentResult;
use App\Domain\Payments\DTOs\RefundRequest;
use App\Domain\Payments\DTOs\RefundResult;
use App\Domain\Payments\ValueObjects\PaymentChargeStatus;

interface PaymentGatewayInterface
{
    public function charge(PaymentRequest $request): PaymentResult;

    public function refund(RefundRequest $request): RefundResult;

    /** Poll acquirer when webhook did not arrive (Option A). */
    public function queryChargeStatus(string $transactionReference): PaymentChargeStatus;
}
