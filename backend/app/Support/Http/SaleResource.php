<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Domain\Shared\Money;
use App\Models\FiscalReceipt;
use App\Models\PaymentLine;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\SalePromotion;

final class SaleResource
{
    /**
     * Compact row for admin sales listing (RN-061).
     *
     * @return array<string, mixed>
     */
    public static function summaryToArray(Sale $sale): array
    {
        $sale->loadMissing(['payments', 'operator', 'store', 'customer']);

        return [
            'id' => $sale->id,
            'store_id' => $sale->store_id,
            'store_code' => $sale->store?->code,
            'operator_id' => $sale->user_id,
            'operator_name' => $sale->operator?->name,
            'customer_id' => $sale->customer_id,
            'customer_name' => $sale->customer?->name,
            'cash_shift_id' => $sale->cash_shift_id,
            'status' => $sale->status->value,
            'subtotal' => Money::toDecimalString((int) $sale->subtotal),
            'discount_total' => Money::toDecimalString((int) $sale->discount_total),
            'total' => Money::toDecimalString((int) $sale->total),
            'completed_at' => $sale->completed_at?->toIso8601String(),
            'payment_methods' => $sale->payments
                ->map(static fn (PaymentLine $payment): string => $payment->method->value)
                ->unique()
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function toArray(Sale $sale): array
    {
        $sale->loadMissing(['lines', 'payments', 'fiscalReceipt', 'salePromotions.promotion']);

        $payload = [
            'id' => $sale->id,
            'store_id' => $sale->store_id,
            'operator_id' => $sale->user_id,
            'cash_shift_id' => $sale->cash_shift_id,
            'customer_id' => $sale->customer_id,
            'status' => $sale->status->value,
            'hold_label' => $sale->hold_label,
            'held_at' => $sale->held_at?->toIso8601String(),
            'subtotal' => Money::toDecimalString((int) $sale->subtotal),
            'discount_total' => Money::toDecimalString((int) $sale->discount_total),
            'total' => Money::toDecimalString((int) $sale->total),
            'completed_at' => $sale->completed_at?->toIso8601String(),
            'lines' => $sale->lines->map(fn (SaleLine $line): array => self::lineToArray($line))->values()->all(),
            'payments' => $sale->payments->map(fn (PaymentLine $payment): array => self::paymentToArray($payment))->values()->all(),
            'promotions' => $sale->salePromotions
                ->map(fn (SalePromotion $applied): array => PromotionResource::appliedToArray($applied))
                ->values()
                ->all(),
        ];

        if ($sale->fiscalReceipt !== null) {
            $payload['fiscal_receipt'] = self::fiscalReceiptToArray($sale->fiscalReceipt);
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    public static function lineToArray(SaleLine $line): array
    {
        return [
            'id' => $line->id,
            'product_id' => $line->product_id,
            'quantity' => $line->quantity,
            'unit_price' => Money::toDecimalString((int) $line->unit_price),
            'line_discount' => Money::toDecimalString((int) $line->line_discount),
            'line_total' => Money::toDecimalString((int) $line->line_total),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function paymentToArray(PaymentLine $payment): array
    {
        return [
            'id' => $payment->id,
            'method' => $payment->method->value,
            'amount' => Money::toDecimalString((int) $payment->amount),
            'cash_received' => $payment->cash_received !== null
                ? Money::toDecimalString((int) $payment->cash_received)
                : null,
            'change_amount' => $payment->change_amount !== null
                ? Money::toDecimalString((int) $payment->change_amount)
                : null,
            'transaction_reference' => $payment->transaction_reference,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function fiscalReceiptToArray(FiscalReceipt $receipt): array
    {
        return [
            'id' => $receipt->id,
            'receipt_number' => $receipt->receipt_number,
            'issued_at' => $receipt->issued_at?->toIso8601String(),
        ];
    }
}
