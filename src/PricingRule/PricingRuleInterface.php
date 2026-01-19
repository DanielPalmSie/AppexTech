<?php

declare(strict_types=1);

namespace App\PricingRule;

use App\Cart;
use App\Catalog;
use App\Money;

/**
 * Pricing rule interface for cart-wide discounts.
 */
interface PricingRuleInterface
{
    /**
     * @param Cart $cart
     * @param Catalog $catalog
     * @return Money Discount/adjustment to apply to the cart total.
     */
    public function calculateDiscount(Cart $cart, Catalog $catalog): Money;
}
