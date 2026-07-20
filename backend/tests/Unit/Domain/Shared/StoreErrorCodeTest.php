<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\ErrorCode;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class StoreErrorCodeTest extends TestCase
{
    public function test_catalog_lists_all_store_error_codes(): void
    {
        $codes = ErrorCode::storeErrors();

        $this->assertCount(4, $codes);
        $this->assertContains(ErrorCode::StoreNotFound, $codes);
        $this->assertContains(ErrorCode::StoreContextRequired, $codes);
        $this->assertContains(ErrorCode::StoreInactive, $codes);
        $this->assertContains(ErrorCode::StoreNotAssigned, $codes);
    }

    #[DataProvider('storeErrorProvider')]
    public function test_each_store_error_has_message_and_http_status(ErrorCode $code): void
    {
        $this->assertNotSame('', $code->message());
        $this->assertContains($code->httpStatus(), [403, 404, 422]);
        $this->assertSame($code->value, $code->toErrorPayload()['code']);
        $this->assertSame($code->message(), $code->toErrorPayload()['message']);
    }

    /**
     * @return array<string, array{0: ErrorCode}>
     */
    public static function storeErrorProvider(): array
    {
        $cases = [];

        foreach (ErrorCode::storeErrors() as $code) {
            $cases[$code->value] = [$code];
        }

        return $cases;
    }
}
