<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

/**
 * Product catalog with SKU pricing.
 */
final class Catalog
{
    /**
     * @var array<string, int>
     */
    private array $prices;

    /**
     * @param array<string, int>|null $prices
     */
    public function __construct(?array $prices = null)
    {
        $this->prices = $prices ?? self::defaultPrices();
    }

    /**
     * @param string $sku
     * @return bool
     */
    public function hasSku(string $sku): bool
    {
        return array_key_exists($sku, $this->prices);
    }

    /**
     * @param string $sku
     * @return Money
     */
    public function priceFor(string $sku): Money
    {
        if (!$this->hasSku($sku)) {
            throw new InvalidArgumentException('Unknown SKU: ' . $sku);
        }

        return Money::fromPence($this->prices[$sku]);
    }

    /**
     * @return array<string, int>
     */
    public function prices(): array
    {
        return $this->prices;
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
