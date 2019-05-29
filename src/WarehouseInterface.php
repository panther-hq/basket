<?php

declare(strict_types=1);

namespace PantherHQ\Basket;

use PantherHQ\Basket\Item\Item;
use PantherHQ\Basket\Item\ItemId;

interface WarehouseInterface
{
    public function add(Item $item, Warehouse $warehouse): void;

    public function remove(Item $item, Warehouse $warehouse): void;

    public function getByItemId(ItemId $itemId, Warehouse $warehouse): Item;

    /**
     * @return Item[]
     */
    public function findAll(Warehouse $warehouse): array;

    public function destroy(Warehouse $warehouse): void;
}
