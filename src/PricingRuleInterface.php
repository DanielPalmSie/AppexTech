<?php

declare(strict_types=1);

namespace App;

/**
 * Pricing rule interface for cart-wide discounts.
 */
interface PricingRuleInterface
{
    /**
     * @param Cart $cart
     * @param array<string, int> $prices Map of sku => unit price in pennies.
     * @return Money Discount/adjustment to apply to the cart total.
     */
    public function calculateDiscount(Cart $cart, array $prices): Money;
}
