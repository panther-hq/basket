<?php

declare(strict_types=1);

namespace Basket\Item;

final class Item
{
    /**
     * @var ItemId
     */
    private $itemId;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var float
     */
    private $price;

    public function __construct(ItemId $itemId, int $quantity, float $price)
    {
        $this->itemId = $itemId;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public function itemId(): ItemId
    {
        return $this->itemId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function price(): float
    {
        return $this->price;
    }
}
