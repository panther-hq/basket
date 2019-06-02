<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Item;

use PantherHQ\Basket\Exception\ItemException;

final class Item implements ItemInterface
{
    /**
     * @var ItemId
     */
    private $itemId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var float
     */
    private $price;

    public function __construct(ItemId $itemId, string $name, int $quantity, float $price)
    {
        if ($quantity <= 0) {
            throw new ItemException(sprintf('quantity for item with id %s can not be %s', $itemId->id(), $quantity));
        }

        if ($price <= 0) {
            throw new ItemException(sprintf('price for item with id %s can not be %s', $itemId->id(), $price));
        }

        $this->itemId = $itemId;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public function itemId(): ItemId
    {
        return $this->itemId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function price(): float
    {
        return $this->price;
    }

    public function total(): float
    {
        return $this->quantity * $this->price;
    }
}
