<?php

declare(strict_types=1);

namespace App\PricingRule;

use App\Cart;
use App\Catalog;
use App\Money;

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
     * @param Catalog $catalog
     * @return Money
     */
    public function calculateDiscount(Cart $cart, Catalog $catalog): Money
    {
        $quantity = $cart->getQuantity($this->sku);
        if ($quantity < $this->threshold) {
            return Money::fromPence(0);
        }

        $standardPrice = $catalog->priceFor($this->sku)->amount();
        $discountPerItem = max(0, $standardPrice - $this->bulkPrice);
        return Money::fromPence($discountPerItem)->multiply($quantity);
    }
}
