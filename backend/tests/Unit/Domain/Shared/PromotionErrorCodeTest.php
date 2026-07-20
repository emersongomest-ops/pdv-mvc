<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\ErrorCode;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class PromotionErrorCodeTest extends TestCase
{
    public function test_catalog_lists_all_promotion_error_codes_for_mvp_slice(): void
    {
        $codes = ErrorCode::promotionErrors();

        $this->assertCount(5, $codes);
        $this->assertContains(ErrorCode::PromoNotCombinable, $codes);
    }

    #[DataProvider('promotionErrorProvider')]
    public function test_each_promotion_error_has_message_and_http_status(ErrorCode $code, int $status): void
    {
        $this->assertNotSame('', $code->message());
        $this->assertSame($status, $code->httpStatus());
        $this->assertSame($code->value, $code->toErrorPayload()['code']);
    }

    /**
     * @return array<string, array{0: ErrorCode, 1: int}>
     */
    public static function promotionErrorProvider(): array
    {
        return [
            'PROMO_NOT_FOUND' => [ErrorCode::PromoNotFound, 404],
            'PROMO_NOT_APPLICABLE' => [ErrorCode::PromoNotApplicable, 422],
            'PROMO_EXPIRED' => [ErrorCode::PromoExpired, 422],
            'PROMO_NOT_ASSIGNED' => [ErrorCode::PromoNotAssigned, 422],
            'PROMO_NOT_COMBINABLE' => [ErrorCode::PromoNotCombinable, 422],
        ];
    }
}
