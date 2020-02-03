<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Item;

interface ItemInterface
{
    public function itemId(): ItemId;

    public function name(): string;

    public function quantity(): int;

    public function price(): float;

    public function total(): float;

    public function attribute(): Attribute;

    public function setAttribute(Attribute $attribute): void;

    public function hasAttribute(): bool;
}
