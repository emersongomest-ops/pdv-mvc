<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\ErrorCode;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class RefundErrorCodeTest extends TestCase
{
    public function test_catalog_lists_all_refund_error_codes_for_mvp_slice(): void
    {
        $codes = ErrorCode::refundErrors();

        $this->assertCount(4, $codes);
        $this->assertContains(ErrorCode::RefAlreadyFullyRefunded, $codes);
    }

    #[DataProvider('refundErrorProvider')]
    public function test_each_refund_error_has_message_and_http_status(ErrorCode $code, int $status): void
    {
        $this->assertNotSame('', $code->message());
        $this->assertSame($status, $code->httpStatus());
        $this->assertSame($code->value, $code->toErrorPayload()['code']);
    }

    /**
     * @return array<string, array{0: ErrorCode, 1: int}>
     */
    public static function refundErrorProvider(): array
    {
        return [
            'REF_AMOUNT_EXCEEDS_SALE' => [ErrorCode::RefAmountExceedsSale, 422],
            'REF_SALE_NOT_FOUND' => [ErrorCode::RefSaleNotFound, 404],
            'REF_ALREADY_FULLY_REFUNDED' => [ErrorCode::RefAlreadyFullyRefunded, 409],
            'REF_RETURN_QTY_INVALID' => [ErrorCode::RefReturnQtyInvalid, 422],
        ];
    }
}
