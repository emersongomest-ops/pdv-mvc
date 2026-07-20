<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use InvalidArgumentException;

/**
 * Integer-cent money helpers.
 *
 * Domain/DB store cents (int). HTTP boundary converts decimal strings ↔ cents.
 * Percent values use the same *100 scale (10.00% → 1000).
 */
final class Money
{
    /**
     * Convert decimal input ("13.00", 13, 13.5) to integer cents.
     *
     * @throws InvalidArgumentException
     */
    public static function fromDecimalInput(string|int|float $value): int
    {
        if (is_int($value)) {
            if ($value < 0) {
                throw new InvalidArgumentException('Money amount must be non-negative.');
            }

            return $value * 100;
        }

        if (is_float($value)) {
            if (! is_finite($value) || $value < 0) {
                throw new InvalidArgumentException('Money amount must be a finite non-negative number.');
            }

            // Format via string path to avoid binary float drift before parsing.
            $value = sprintf('%.2F', round($value, 2));
        }

        $normalized = trim($value);

        if ($normalized === '' || ! preg_match('/^\d+(\.\d{1,2})?$/', $normalized)) {
            throw new InvalidArgumentException('Invalid money decimal format.');
        }

        if (str_contains($normalized, '.')) {
            [$whole, $fraction] = explode('.', $normalized, 2);
            $fraction = str_pad($fraction, 2, '0');
        } else {
            $whole = $normalized;
            $fraction = '00';
        }

        return ((int) $whole) * 100 + (int) $fraction;
    }

    public static function toDecimalString(int $cents): string
    {
        if ($cents < 0) {
            throw new InvalidArgumentException('Money amount must be non-negative.');
        }

        return sprintf('%d.%02d', intdiv($cents, 100), $cents % 100);
    }

    /** Format cents allowing negative (e.g. cash variance). */
    public static function toDecimalStringSigned(int $cents): string
    {
        $sign = $cents < 0 ? '-' : '';
        $absolute = abs($cents);

        return $sign.sprintf('%d.%02d', intdiv($absolute, 100), $absolute % 100);
    }

    public static function add(int $a, int $b): int
    {
        return $a + $b;
    }

    public static function sub(int $a, int $b): int
    {
        return $a - $b;
    }

    public static function mulQty(int $cents, int $qty): int
    {
        if ($qty < 0) {
            throw new InvalidArgumentException('Quantity must be non-negative.');
        }

        return $cents * $qty;
    }

    /**
     * Percent of subtotal. percentScaled 1000 = 10.00%.
     */
    public static function percentOf(int $subtotalCents, int $percentScaled): int
    {
        if ($subtotalCents < 0 || $percentScaled < 0) {
            throw new InvalidArgumentException('Percent inputs must be non-negative.');
        }

        return intdiv($subtotalCents * $percentScaled, 10000);
    }
}
