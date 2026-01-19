<?php

declare(strict_types=1);

namespace App\PricingRule;

use App\Cart;
use App\Catalog;
use App\Money;

/**
 * Buy-one-get-one-free rule for a specific SKU.
 */
final class BogofRule implements PricingRuleInterface
{
    /**
     * @var string
     */
    private string $sku;

    /**
     * @param string $sku
     */
    public function __construct(string $sku)
    {
        $this->sku = $sku;
    }

    /**
     * @param Cart $cart
     * @param Catalog $catalog
     * @return Money
     */
    public function calculateDiscount(Cart $cart, Catalog $catalog): Money
    {
        $quantity = $cart->getQuantity($this->sku);
        if ($quantity < 2) {
            return Money::fromPence(0);
        }

        $freeItems = intdiv($quantity, 2);
        return $catalog->priceFor($this->sku)->multiply($freeItems);
    }
}
