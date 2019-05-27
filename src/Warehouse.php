<?php

declare(strict_types=1);

namespace Basket;

final class Warehouse
{
    /**
     * @var string
     */
    private $warehouseId;

    public function warehouseId(): string
    {
        return $this->warehouseId;
    }

    public function setWarehouseId(string $warehouseId): void
    {
        $this->warehouseId = $warehouseId;
    }
}
