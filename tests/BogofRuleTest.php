<?php

declare(strict_types=1);

namespace App\Tests;

use App\Cart;
use App\Catalog;
use App\PricingRule\BogofRule;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\PricingRule\BogofRule
 * @covers \App\Cart
 * @covers \App\Catalog
 * @covers \App\Money
 */
final class BogofRuleTest extends TestCase
{
    public function testBogofEvenQuantity(): void
    {
        $cart = new Cart();
        $cart->add('FR1', 2);

        $discount = (new BogofRule('FR1'))->calculateDiscount($cart, new Catalog());

        $this->assertSame(311, $discount->amount());
    }

    public function testBogofOddQuantity(): void
    {
        $cart = new Cart();
        $cart->add('FR1', 3);

        $discount = (new BogofRule('FR1'))->calculateDiscount($cart, new Catalog());

        $this->assertSame(311, $discount->amount());
    }

    public function testBogofBelowTwoReturnsZero(): void
    {
        $cart = new Cart();
        $cart->add('FR1', 1);

        $discount = (new BogofRule('FR1'))->calculateDiscount($cart, new Catalog());

        $this->assertSame(0, $discount->amount());
    }
}
