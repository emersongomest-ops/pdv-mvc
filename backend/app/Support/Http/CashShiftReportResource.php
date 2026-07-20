<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Domain\CashShift\DTOs\ShiftClosingReport;
use App\Domain\Shared\Money;

final class CashShiftReportResource
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(ShiftClosingReport $report): array
    {
        return [
            'shift_id' => $report->shiftId,
            'store_id' => $report->storeId,
            'store_code' => $report->storeCode,
            'operator_id' => $report->operatorId,
            'operator_name' => $report->operatorName,
            'status' => $report->status,
            'sales_count' => $report->salesCount,
            'sales_total' => Money::toDecimalString($report->salesTotalCents),
            'totals_by_payment_method' => array_map(
                static fn (array $row): array => [
                    'method' => $row['method'],
                    'amount' => Money::toDecimalString($row['amount_cents']),
                ],
                $report->totalsByPaymentMethod,
            ),
            'opening_cash_amount' => Money::toDecimalString($report->openingCashCents),
            'expected_cash_amount' => Money::toDecimalString($report->expectedCashCents),
            'closing_cash_amount' => $report->closingCashCents !== null
                ? Money::toDecimalString($report->closingCashCents)
                : null,
            'cash_variance' => $report->cashVarianceCents !== null
                ? Money::toDecimalStringSigned($report->cashVarianceCents)
                : null,
            'opened_at' => $report->openedAt,
            'closed_at' => $report->closedAt,
        ];
    }
}
