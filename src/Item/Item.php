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
     * @var ProductId
     */
    private $productId;

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

    /**
     * @var Attribute
     */
    private $attribute;

    /**
     * @var \DateTimeImmutable
     */
    private $addedAt;

    public function __construct(ItemId $itemId, ProductId $productId, string $name, int $quantity, float $price, ?\DateTimeImmutable $addedAt)
    {
        if ($quantity <= 0) {
            throw new ItemException(sprintf('quantity for item with id %s can not be %s', $itemId->id(), $quantity));
        }

        if ($price <= 0) {
            throw new ItemException(sprintf('price for item with id %s can not be %s', $itemId->id(), $price));
        }

        $this->itemId = $itemId;
        $this->productId = $productId;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
        if($addedAt !== null) {
            $this->addedAt = $addedAt;
        } else {
            $this->addedAt = new \DateTimeImmutable('now');
        }


    }

    public function itemId(): ItemId
    {
        return $this->itemId;
    }

    public function setItemId(ItemId $itemId): void
    {
        $this->itemId = $itemId;
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function price(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function total(): float
    {
        return $this->quantity * $this->price;
    }

    public function addedAt(): \DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeImmutable $addedAt): void
    {
        $this->addedAt = $addedAt;
    }

    public function attribute(): Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(?Attribute $attribute): void
    {
        $this->attribute = $attribute;
    }

    public function hasAttribute(): bool
    {
        return null !== $this->attribute;
    }

    public function toArray(): array
    {
        return [
            'item' => $this,
            'itemId' => $this->itemId()->id(),
            'productId' => $this->productId()->id(),
            'name' => $this->name(),
            'qty' => $this->quantity(),
            'price' => $this->price(),
        ];
    }
}
