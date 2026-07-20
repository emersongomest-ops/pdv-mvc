<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\ErrorCode;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class PaymentErrorCodeTest extends TestCase
{
    public function test_catalog_lists_all_payment_error_codes_for_mvp_slice(): void
    {
        $codes = ErrorCode::paymentErrors();

        $this->assertCount(15, $codes);
        $this->assertContains(ErrorCode::PayMethodUnsupported, $codes);
        $this->assertContains(ErrorCode::PayCashInsufficient, $codes);
        $this->assertContains(ErrorCode::PayGatewayUnavailable, $codes);
        $this->assertContains(ErrorCode::PayMethodNotImplemented, $codes);
        $this->assertContains(ErrorCode::PayWebhookInvalidSignature, $codes);
        $this->assertContains(ErrorCode::PayWebhookUnknownReference, $codes);
    }

    #[DataProvider('paymentErrorProvider')]
    public function test_each_payment_error_has_message_and_http_status(ErrorCode $code): void
    {
        $this->assertNotSame('', $code->message());
        $this->assertSame($code->value, $code->toErrorPayload()['code']);
        $this->assertContains($code->httpStatus(), [401, 404, 422, 501, 503]);
    }

    /**
     * @return array<string, array{0: ErrorCode}>
     */
    public static function paymentErrorProvider(): array
    {
        $cases = [];

        foreach (ErrorCode::paymentErrors() as $code) {
            $cases[$code->value] = [$code];
        }

        return $cases;
    }
}
