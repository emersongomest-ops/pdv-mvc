<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\ErrorCode;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class AuthErrorCodeTest extends TestCase
{
    public function test_catalog_lists_all_authentication_error_codes(): void
    {
        $codes = ErrorCode::authenticationErrors();

        $this->assertCount(20, $codes);
        $this->assertSame(
            array_map(static fn (ErrorCode $code): string => $code->value, $codes),
            array_map(static fn (ErrorCode $code): string => $code->value, ErrorCode::authenticationErrors())
        );
    }

    #[DataProvider('authenticationErrorProvider')]
    public function test_each_authentication_error_has_message_and_http_status(ErrorCode $code): void
    {
        $this->assertNotSame('', $code->message());
        $this->assertContains($code->httpStatus(), [401, 403, 404, 409, 422, 423, 429]);
        $this->assertSame($code->value, $code->toErrorPayload()['code']);
        $this->assertSame($code->message(), $code->toErrorPayload()['message']);
    }

    /**
     * @return array<string, array{0: ErrorCode}>
     */
    public static function authenticationErrorProvider(): array
    {
        $cases = [];

        foreach (ErrorCode::authenticationErrors() as $code) {
            $cases[$code->value] = [$code];
        }

        return $cases;
    }
}
