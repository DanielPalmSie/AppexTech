<?php

declare(strict_types=1);

namespace App\Tests;

use App\Catalog;
use App\Checkout;
use App\Money;
use App\PricingRule\BogofRule;
use App\PricingRule\BulkPriceRule;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Checkout
 * @covers \App\Catalog
 * @covers \App\Money
 * @covers \App\Cart
 */
final class CheckoutTest extends TestCase
{
    public function testOrderIndependentScanning(): void
    {
        $rules = [
            new BogofRule('FR1'),
            new BulkPriceRule('SR1', 3, 450),
        ];

        $checkout = new Checkout($rules);
        $checkout->scan('CF1');
        $checkout->scan('FR1');
        $checkout->scan('SR1');

        $this->assertSame('£19.34', $checkout->total());
    }

    public function testCombinedRulesExampleBasketOne(): void
    {
        $rules = [
            new BogofRule('FR1'),
            new BulkPriceRule('SR1', 3, 450),
        ];

        $checkout = new Checkout($rules);
        foreach (['FR1', 'SR1', 'FR1', 'FR1', 'CF1'] as $sku) {
            $checkout->scan($sku);
        }

        $this->assertSame('£22.45', $checkout->total());
    }

    public function testCombinedRulesExampleBasketTwo(): void
    {
        $rules = [
            new BogofRule('FR1'),
            new BulkPriceRule('SR1', 3, 450),
        ];

        $checkout = new Checkout($rules);
        $checkout->scan('FR1');
        $checkout->scan('FR1');

        $this->assertSame('£3.11', $checkout->total());
    }

    public function testCombinedRulesExampleBasketThree(): void
    {
        $rules = [
            new BogofRule('FR1'),
            new BulkPriceRule('SR1', 3, 450),
        ];

        $checkout = new Checkout($rules);
        foreach (['SR1', 'SR1', 'FR1', 'SR1'] as $sku) {
            $checkout->scan($sku);
        }

        $this->assertSame('£16.61', $checkout->total());
    }

    public function testMoneyOperationsAndFormatting(): void
    {
        $money = Money::fromPence(100)->add(Money::fromPence(50));

        $this->assertSame(150, $money->amount());
        $this->assertSame('£1.50', $money->format());
        $this->assertSame('£1.50', (string) $money);
        $this->assertSame(100, $money->subtract(Money::fromPence(50))->amount());
        $this->assertSame(300, $money->multiply(2)->amount());
    }

    public function testUnknownSkuThrowsWhenScanning(): void
    {
        $checkout = new Checkout([]);

        $this->expectException(InvalidArgumentException::class);
        $checkout->scan('NOPE');
    }

    public function testCatalogRejectsUnknownSku(): void
    {
        $catalog = new Catalog();

        $this->expectException(InvalidArgumentException::class);
        $catalog->priceFor('NOPE');
    }
}
