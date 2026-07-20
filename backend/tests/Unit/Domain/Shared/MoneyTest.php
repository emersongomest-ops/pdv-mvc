<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\Money;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function test_from_decimal_input_parses_string_int_and_float(): void
    {
        $this->assertSame(1300, Money::fromDecimalInput('13.00'));
        $this->assertSame(1300, Money::fromDecimalInput(13));
        $this->assertSame(1350, Money::fromDecimalInput(13.5));
        $this->assertSame(749, Money::fromDecimalInput(7.49));
        $this->assertSame(1000, Money::fromDecimalInput('10'));
        $this->assertSame(0, Money::fromDecimalInput('0.00'));
    }

    public function test_from_decimal_input_rejects_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Money::fromDecimalInput('13.001');
    }

    public function test_to_decimal_string(): void
    {
        $this->assertSame('13.00', Money::toDecimalString(1300));
        $this->assertSame('0.00', Money::toDecimalString(0));
        $this->assertSame('7.49', Money::toDecimalString(749));
    }

    public function test_arithmetic_helpers(): void
    {
        $this->assertSame(1500, Money::add(1000, 500));
        $this->assertSame(500, Money::sub(1000, 500));
        $this->assertSame(3000, Money::mulQty(1500, 2));
        $this->assertSame(1000, Money::percentOf(10000, 1000));
    }
}
