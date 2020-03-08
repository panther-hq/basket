<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Item;

interface ItemInterface
{
    public function itemId(): ItemId;

    public function productId(): ProductId;

    public function hasProductId(): bool;

    public function name(): string;

    public function quantity(): int;

    public function price(): float;

    public function total(): float;

    public function hasAddedAt(): bool;

    public function addedAt(): \DateTimeImmutable;

    public function setAddedAt(\DateTimeImmutable $addedAt): void;

    public function attribute(): Attribute;

    public function setAttribute(Attribute $attribute): void;

    public function hasAttribute(): bool;

    public function jsonSerialize(): array;

    public function setProductId(ProductId $productId): void;

    public function setItemId(ItemId $itemId): void;

    public function setQuantity(int $quantity): void;

    public function setPrice(float $price): void;
}
