<?php

declare(strict_types=1);

namespace App\Tests;

use App\Cart;
use App\Catalog;
use App\PricingRule\BulkPriceRule;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\PricingRule\BulkPriceRule
 * @covers \App\Cart
 * @covers \App\Catalog
 * @covers \App\Money
 */
final class BulkPriceRuleTest extends TestCase
{
    public function testBulkPriceBelowThreshold(): void
    {
        $cart = new Cart();
        $cart->add('SR1', 2);

        $discount = (new BulkPriceRule('SR1', 3, 450))->calculateDiscount($cart, new Catalog());

        $this->assertSame(0, $discount->amount());
    }

    public function testBulkPriceAboveThreshold(): void
    {
        $cart = new Cart();
        $cart->add('SR1', 3);

        $discount = (new BulkPriceRule('SR1', 3, 450))->calculateDiscount($cart, new Catalog());

        $this->assertSame(150, $discount->amount());
    }
}
