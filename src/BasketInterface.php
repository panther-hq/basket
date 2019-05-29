<?php

declare(strict_types=1);

namespace PantherHQ\Basket;

use PantherHQ\Basket\Item\ItemInterface;

interface BasketInterface
{
    public function warehouse(): ?Warehouse;

    public function hasWarehouse(): bool;

    public function setWarehouseId(string $warehouseId): void;

    /**
     * @param ItemInterface[] $items
     */
    public function add(array $items): void;

    /**
     * @return ItemInterface[]
     */
    public function findAll(): array;

    /**
     * @param ItemInterface[] $items
     */
    public function remove(array $items): void;

    public function destroy(): void;

    public function mergeWarehouse(Warehouse $warehouse): void;

    public function total(): float;
}
