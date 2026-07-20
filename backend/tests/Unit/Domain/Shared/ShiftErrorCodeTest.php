<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\ErrorCode;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class ShiftErrorCodeTest extends TestCase
{
    public function test_catalog_lists_all_shift_error_codes(): void
    {
        $codes = ErrorCode::shiftErrors();

        $this->assertCount(5, $codes);
        $this->assertContains(ErrorCode::ShiftNotOpen, $codes);
        $this->assertContains(ErrorCode::ShiftAlreadyOpen, $codes);
        $this->assertContains(ErrorCode::ShiftStoreMismatch, $codes);
        $this->assertContains(ErrorCode::ShiftReopenDenied, $codes);
        $this->assertContains(ErrorCode::ShiftNotFound, $codes);
    }

    #[DataProvider('shiftErrorProvider')]
    public function test_each_shift_error_has_message_and_http_status(ErrorCode $code): void
    {
        $this->assertNotSame('', $code->message());
        $this->assertContains($code->httpStatus(), [403, 404, 409, 422]);
        $this->assertSame($code->value, $code->toErrorPayload()['code']);
        $this->assertSame($code->message(), $code->toErrorPayload()['message']);
    }

    /**
     * @return array<string, array{0: ErrorCode}>
     */
    public static function shiftErrorProvider(): array
    {
        $cases = [];

        foreach (ErrorCode::shiftErrors() as $code) {
            $cases[$code->value] = [$code];
        }

        return $cases;
    }
}
