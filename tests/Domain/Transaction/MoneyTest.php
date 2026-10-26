<?php

declare(strict_types=1);

namespace Tests\Domain\Transaction;

use App\Domain\Transaction\Money;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testItConvertsDecimalAmountToMinorUnits(): void
    {
        $money = Money::fromDecimal('12.50', 'EUR');

        self::assertSame(1250, $money->minorUnits);
        self::assertSame('EUR', $money->currency);
    }

    public function testItRejectsInvalidCurrency(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Money(100, 'EURO');
    }
}
