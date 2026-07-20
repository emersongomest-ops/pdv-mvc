<?php

declare(strict_types=1);

namespace App\Domain\Audit\ValueObjects;

enum AuditAction: string
{
    case ProductPriceChanged = 'catalog.product.price_changed';
    case StockAdjusted = 'inventory.stock_adjusted';
    case RefundCreated = 'refund.created';
    case ReturnCreated = 'return.created';
    case PromotionCreated = 'promotion.created';
    case PromotionUpdated = 'promotion.updated';
    case CashShiftReopened = 'cash_shift.reopened';
    case ManagerMfaReset = 'identity.mfa_reset';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_values(array_column(self::cases(), 'value'));
    }
}
