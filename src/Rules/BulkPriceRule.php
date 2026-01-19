<?php

declare(strict_types=1);

namespace App\Rules;

use App\Cart;
use App\Money;
use App\PricingRuleInterface;

/**
 * Bulk price rule that reduces unit price when a threshold is met.
 */
final class BulkPriceRule implements PricingRuleInterface
{
    /**
     * @var string
     */
    private string $sku;

    /**
     * @var int
     */
    private int $threshold;

    /**
     * @var int
     */
    private int $bulkPrice;

    /**
     * @param string $sku
     * @param int $threshold
     * @param int $bulkPrice Bulk unit price in pennies.
     */
    public function __construct(string $sku, int $threshold, int $bulkPrice)
    {
        $this->sku = $sku;
        $this->threshold = $threshold;
        $this->bulkPrice = $bulkPrice;
    }

    /**
     * @param Cart $cart
     * @param array<string, int> $prices
     * @return Money
     */
    public function calculateDiscount(Cart $cart, array $prices): Money
    {
        $quantity = $cart->getQuantity($this->sku);
        if ($quantity < $this->threshold) {
            return Money::fromPence(0);
        }

        $standardPrice = $prices[$this->sku];
        $discountPerItem = $standardPrice - $this->bulkPrice;
        return Money::fromPence($discountPerItem)->multiply($quantity);
    }
}
