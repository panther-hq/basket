<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Item;

use Shop\Application\Exception\RuntimeException;

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

    public function addedAt(): ?\DateTimeImmutable;

    public function setAddedAt(\DateTimeImmutable $addedAt): void;

    public function attribute(): Attribute;

    public function setAttribute(?Attribute $attribute): void;

    public function hasAttribute(): bool;

    public function toArray();

    // Only for adding so we don't break logged in user carts
    public function setProductId(ProductId $productId);

}
