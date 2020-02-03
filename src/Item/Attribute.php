<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Item;

final class Attribute
{
    /**
     * @var string
     */
    private $promotion;

    public function promotion(): string
    {
        return $this->promotion;
    }

    public function setPromotion(string $promotion): void
    {
        $this->promotion = $promotion;
    }

    public function hasPromotion(): bool
    {
        return $this->promotion !== null;
    }
}
