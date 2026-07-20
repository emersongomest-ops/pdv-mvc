<?php

declare(strict_types=1);

namespace App\Domain\Payments\ValueObjects;

enum PaymentWebhookEventType: string
{
    case PaymentConfirmed = 'payment.confirmed';
    case PaymentFailed = 'payment.failed';
}
