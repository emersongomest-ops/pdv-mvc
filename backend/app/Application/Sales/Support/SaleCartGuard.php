<?php

declare(strict_types=1);

namespace App\Application\Sales\Support;

use App\Domain\Sales\Exceptions\SaleDomainException;
use App\Domain\Sales\ValueObjects\SaleStatus;
use App\Domain\Shared\ErrorCode;
use App\Models\Sale;

final class SaleCartGuard
{
    public static function assertMutable(Sale $sale, int $storeId, int $userId, int $cashShiftId): void
    {
        self::assertAccessible($sale, $storeId, $userId, $cashShiftId);

        if ($sale->status === SaleStatus::Completed) {
            throw new SaleDomainException(ErrorCode::SaleAlreadyCompleted);
        }

        if ($sale->status === SaleStatus::Held) {
            throw new SaleDomainException(ErrorCode::SaleCartHeld);
        }
    }

    public static function assertAccessible(Sale $sale, int $storeId, int $userId, int $cashShiftId): void
    {
        if (
            $sale->store_id !== $storeId
            || $sale->user_id !== $userId
            || $sale->cash_shift_id !== $cashShiftId
        ) {
            throw new SaleDomainException(ErrorCode::SaleNotFound);
        }
    }

    public static function assertHoldable(Sale $sale, int $storeId, int $userId, int $cashShiftId): void
    {
        self::assertMutable($sale, $storeId, $userId, $cashShiftId);

        if ($sale->lines->isEmpty()) {
            throw new SaleDomainException(ErrorCode::SaleEmptyCart);
        }
    }

    public static function assertResumable(Sale $sale, int $storeId, int $userId, int $cashShiftId): void
    {
        self::assertAccessible($sale, $storeId, $userId, $cashShiftId);

        if ($sale->status !== SaleStatus::Held) {
            throw new SaleDomainException(ErrorCode::SaleNotHeld);
        }
    }
}
