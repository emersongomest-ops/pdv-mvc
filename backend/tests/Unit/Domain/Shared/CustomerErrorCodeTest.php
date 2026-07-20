<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\ErrorCode;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class CustomerErrorCodeTest extends TestCase
{
    public function test_catalog_lists_all_customer_error_codes_for_mvp_slice(): void
    {
        $codes = ErrorCode::customerErrors();

        $this->assertCount(3, $codes);
        $this->assertContains(ErrorCode::CustNotFound, $codes);
        $this->assertContains(ErrorCode::CustCpfDuplicate, $codes);
        $this->assertContains(ErrorCode::CustRequiredFieldMissing, $codes);
    }

    #[DataProvider('customerErrorProvider')]
    public function test_each_customer_error_has_message_and_http_status(ErrorCode $code, int $status): void
    {
        $this->assertNotSame('', $code->message());
        $this->assertSame($status, $code->httpStatus());
        $this->assertSame($code->value, $code->toErrorPayload()['code']);
    }

    /**
     * @return array<string, array{0: ErrorCode, 1: int}>
     */
    public static function customerErrorProvider(): array
    {
        return [
            'CUST_NOT_FOUND' => [ErrorCode::CustNotFound, 404],
            'CUST_CPF_DUPLICATE' => [ErrorCode::CustCpfDuplicate, 409],
            'CUST_REQUIRED_FIELD_MISSING' => [ErrorCode::CustRequiredFieldMissing, 422],
        ];
    }
}
