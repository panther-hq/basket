<?php

declare(strict_types=1);

namespace Basket;

use Basket\Item\Item;
use Basket\Item\ItemId;

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
