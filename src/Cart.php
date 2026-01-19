<?php

declare(strict_types=1);

namespace App;

/**
 * Cart storing items as sku => quantity.
 */
final class Cart
{
    /**
     * @var array<string, int>
     */
    private array $items = [];

    /**
     * @param string $sku
     * @param int $quantity
     */
    public function add(string $sku, int $quantity = 1): void
    {
        $this->items[$sku] = ($this->items[$sku] ?? 0) + $quantity;
    }

    /**
     * @return array<string, int>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param string $sku
     * @return int
     */
    public function getQuantity(string $sku): int
    {
        return $this->items[$sku] ?? 0;
    }
}
