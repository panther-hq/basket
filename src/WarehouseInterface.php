<?php

declare(strict_types=1);

namespace PantherHQ\Basket;

use PantherHQ\Basket\Item\ItemId;
use PantherHQ\Basket\Item\ItemInterface;

interface WarehouseInterface
{
    public function add(ItemInterface $item, Warehouse $warehouse): void;

    public function remove(ItemInterface $item, Warehouse $warehouse): void;

    public function getByItemId(ItemId $itemId, Warehouse $warehouse): ItemInterface;

    /**
     * @return ItemInterface[]
     */
    public function findAll(Warehouse $warehouse): array;

    public function destroy(Warehouse $warehouse): void;
}
