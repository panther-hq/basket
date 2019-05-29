<?php

declare(strict_types=1);

namespace PantherHQ\Basket;

use Cocur\Slugify\Slugify;

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
        $this->warehouseId = (new Slugify())->slugify($warehouseId);
    }
}
