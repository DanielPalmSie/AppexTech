<?php

declare(strict_types=1);

namespace App;

/**
 * Value object for monetary amounts stored in integer pennies.
 */
final class Money
{
    /**
     * @var int
     */
    private int $amount;

    /**
     * @param int $amount Amount in pennies.
     */
    private function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @param int $amount Amount in pennies.
     */
    public static function fromPence(int $amount): self
    {
        return new self($amount);
    }

    /**
     * @return int Amount in pennies.
     */
    public function amount(): int
    {
        return $this->amount;
    }

    /**
     * @param Money $other
     */
    public function add(Money $other): self
    {
        return new self($this->amount + $other->amount());
    }

    /**
     * @param Money $other
     */
    public function subtract(Money $other): self
    {
        return new self($this->amount - $other->amount());
    }

    /**
     * @param int $multiplier
     */
    public function multiply(int $multiplier): self
    {
        return new self($this->amount * $multiplier);
    }

    /**
     * @return string
     */
    public function format(): string
    {
        return '£' . number_format($this->amount / 100, 2, '.', '');
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->format();
    }
}
