<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\ErrorCode;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class InventoryErrorCodeTest extends TestCase
{
    public function test_catalog_lists_all_inventory_error_codes_for_mvp_slice(): void
    {
        $codes = ErrorCode::inventoryErrors();

        $this->assertCount(3, $codes);
        $this->assertContains(ErrorCode::InvProductInactive, $codes);
    }

    #[DataProvider('inventoryErrorProvider')]
    public function test_each_inventory_error_has_message_and_http_status(ErrorCode $code): void
    {
        $this->assertNotSame('', $code->message());
        $this->assertSame(422, $code->httpStatus());
        $this->assertSame($code->value, $code->toErrorPayload()['code']);
    }

    /**
     * @return array<string, array{0: ErrorCode}>
     */
    public static function inventoryErrorProvider(): array
    {
        $cases = [];

        foreach (ErrorCode::inventoryErrors() as $code) {
            $cases[$code->value] = [$code];
        }

        return $cases;
    }
}
