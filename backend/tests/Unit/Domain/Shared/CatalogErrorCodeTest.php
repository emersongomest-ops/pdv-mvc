<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\ErrorCode;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class CatalogErrorCodeTest extends TestCase
{
    public function test_catalog_lists_all_catalog_error_codes(): void
    {
        $codes = ErrorCode::catalogErrors();

        $this->assertCount(6, $codes);
        $this->assertContains(ErrorCode::CatProductNotFound, $codes);
    }

    #[DataProvider('catalogErrorProvider')]
    public function test_each_catalog_error_has_message_and_http_status(ErrorCode $code): void
    {
        $this->assertNotSame('', $code->message());
        $this->assertContains($code->httpStatus(), [404, 409]);
        $this->assertSame($code->value, $code->toErrorPayload()['code']);
    }

    /**
     * @return array<string, array{0: ErrorCode}>
     */
    public static function catalogErrorProvider(): array
    {
        $cases = [];

        foreach (ErrorCode::catalogErrors() as $code) {
            $cases[$code->value] = [$code];
        }

        return $cases;
    }
}
