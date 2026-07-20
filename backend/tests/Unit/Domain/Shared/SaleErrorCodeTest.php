<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\ErrorCode;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class SaleErrorCodeTest extends TestCase
{
    public function test_catalog_lists_all_sale_error_codes(): void
    {
        $codes = ErrorCode::saleErrors();

        $this->assertCount(10, $codes);
        $this->assertContains(ErrorCode::SaleNotFound, $codes);
        $this->assertContains(ErrorCode::SaleLineNotFound, $codes);
        $this->assertContains(ErrorCode::SaleEmptyCart, $codes);
        $this->assertContains(ErrorCode::SaleAlreadyCompleted, $codes);
    }

    #[DataProvider('saleErrorProvider')]
    public function test_each_sale_error_has_message_and_http_status(ErrorCode $code): void
    {
        $this->assertNotSame('', $code->message());
        $this->assertContains($code->httpStatus(), [404, 409, 422, 500]);
        $this->assertSame($code->value, $code->toErrorPayload()['code']);
        $this->assertSame($code->message(), $code->toErrorPayload()['message']);
    }

    /**
     * @return array<string, array{0: ErrorCode}>
     */
    public static function saleErrorProvider(): array
    {
        $cases = [];

        foreach (ErrorCode::saleErrors() as $code) {
            $cases[$code->value] = [$code];
        }

        return $cases;
    }
}
