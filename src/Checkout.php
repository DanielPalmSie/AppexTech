<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;
use App\PricingRule\PricingRuleInterface;

/**
 * Checkout for scanning items and calculating totals with pricing rules.
 */
final class Checkout
{
    /**
     * @var array<int, PricingRuleInterface>
     */
    private array $pricingRules;

    /**
     * @var Cart
     */
    private Cart $cart;

    /**
     * @var Catalog
     */
    private Catalog $catalog;

    /**
     * @param array<int, PricingRuleInterface> $pricingRules
     * @param Catalog|null $catalog
     */
    public function __construct(array $pricingRules, ?Catalog $catalog = null)
    {
        $this->pricingRules = $pricingRules;
        $this->cart = new Cart();
        $this->catalog = $catalog ?? new Catalog();
    }

    /**
     * @param string $sku
     */
    public function scan(string $sku): void
    {
        if (!$this->catalog->hasSku($sku)) {
            throw new InvalidArgumentException('Unknown SKU: ' . $sku);
        }

        $this->cart->add($sku);
    }

    /**
     * @return Money
     */
    public function totalMoney(): Money
    {
        $total = Money::fromPence(0);

        foreach ($this->cart->getItems() as $sku => $quantity) {
            $total = $total->add($this->catalog->priceFor($sku)->multiply($quantity));
        }

        foreach ($this->pricingRules as $rule) {
            $total = $total->subtract($rule->calculateDiscount($this->cart, $this->catalog));
        }

        return $total;
    }

    /**
     * @return string Formatted total with currency symbol.
     */
    public function total(): string
    {
        return $this->totalMoney()->format();
    }
}
