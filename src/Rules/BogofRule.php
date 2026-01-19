<?php

declare(strict_types=1);

namespace App\Rules;

use App\Cart;
use App\Money;
use App\PricingRuleInterface;

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
     * @param array<string, int> $prices
     * @return Money
     */
    public function calculateDiscount(Cart $cart, array $prices): Money
    {
        $quantity = $cart->getQuantity($this->sku);
        if ($quantity < 2) {
            return Money::fromPence(0);
        }

        $freeItems = intdiv($quantity, 2);
        return Money::fromPence($prices[$this->sku])->multiply($freeItems);
    }
}
