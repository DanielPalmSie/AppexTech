<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

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
     * @var array<string, int>
     */
    private array $prices;

    /**
     * @param array<int, PricingRuleInterface> $pricingRules
     * @param array<string, int>|null $prices
     */
    public function __construct(array $pricingRules, ?array $prices = null)
    {
        $this->pricingRules = $pricingRules;
        $this->cart = new Cart();
        $this->prices = $prices ?? self::defaultPrices();
    }

    /**
     * @param string $sku
     */
    public function scan(string $sku): void
    {
        if (!array_key_exists($sku, $this->prices)) {
            throw new InvalidArgumentException('Unknown SKU: ' . $sku);
        }

        $this->cart->add($sku);
    }

    /**
     * @return string Formatted total with currency symbol.
     */
    public function total(): string
    {
        $total = Money::fromPence(0);

        foreach ($this->cart->getItems() as $sku => $quantity) {
            $total = $total->add(Money::fromPence($this->prices[$sku])->multiply($quantity));
        }

        foreach ($this->pricingRules as $rule) {
            $total = $total->subtract($rule->calculateDiscount($this->cart, $this->prices));
        }

        return $total->format();
    }

    /**
     * @return array<string, int>
     */
    private static function defaultPrices(): array
    {
        return [
            'FR1' => 311,
            'SR1' => 500,
            'CF1' => 1123,
        ];
    }
}
