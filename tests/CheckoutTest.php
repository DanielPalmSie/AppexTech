<?php

declare(strict_types=1);

namespace App\Tests;

use App\Cart;
use App\Checkout;
use App\Money;
use App\Rules\BogofRule;
use App\Rules\BulkPriceRule;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Checkout
 * @covers \App\Cart
 * @covers \App\Money
 * @covers \App\Rules\BogofRule
 * @covers \App\Rules\BulkPriceRule
 */
final class CheckoutTest extends TestCase
{
    /**
     * @return array<string, int>
     */
    private function prices(): array
    {
        return [
            'FR1' => 311,
            'SR1' => 500,
            'CF1' => 1123,
        ];
    }

    public function testBogofEvenQuantity(): void
    {
        $cart = new Cart();
        $cart->add('FR1', 2);

        $discount = (new BogofRule('FR1'))->calculateDiscount($cart, $this->prices());

        $this->assertSame(311, $discount->amount());
    }

    public function testBogofOddQuantity(): void
    {
        $cart = new Cart();
        $cart->add('FR1', 3);

        $discount = (new BogofRule('FR1'))->calculateDiscount($cart, $this->prices());

        $this->assertSame(311, $discount->amount());
    }

    public function testBulkPriceBelowThreshold(): void
    {
        $cart = new Cart();
        $cart->add('SR1', 2);

        $discount = (new BulkPriceRule('SR1', 3, 450))->calculateDiscount($cart, $this->prices());

        $this->assertSame(0, $discount->amount());
    }

    public function testBulkPriceAboveThreshold(): void
    {
        $cart = new Cart();
        $cart->add('SR1', 3);

        $discount = (new BulkPriceRule('SR1', 3, 450))->calculateDiscount($cart, $this->prices());

        $this->assertSame(150, $discount->amount());
    }

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

        $checkout = new Checkout($rules, $this->prices());
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

        $checkout = new Checkout($rules, $this->prices());
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

        $checkout = new Checkout($rules, $this->prices());
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

    public function testUnknownSkuThrows(): void
    {
        $checkout = new Checkout([]);

        $this->expectException(InvalidArgumentException::class);
        $checkout->scan('NOPE');
    }
}
