<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Item;

interface ItemInterface
{
    public function itemId(): ItemId;

    public function quantity(): int;

    public function price(): float;
}
