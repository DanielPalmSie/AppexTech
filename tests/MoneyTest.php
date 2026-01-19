<?php

declare(strict_types=1);

namespace App\Tests;

use App\Money;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Money
 */
final class MoneyTest extends TestCase
{
    public function testMoneyOperationsAndFormatting(): void
    {
        $money = Money::fromPence(100)->add(Money::fromPence(50));

        $this->assertSame(150, $money->amount());
        $this->assertSame('£1.50', $money->format());
        $this->assertSame('£1.50', (string) $money);
        $this->assertSame(100, $money->subtract(Money::fromPence(50))->amount());
        $this->assertSame(300, $money->multiply(2)->amount());
    }
}
