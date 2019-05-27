<?php

declare(strict_types=1);

namespace Basket;

use Basket\Item\Item;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class Basket
{
    /**
     * @var WarehouseInterface
     */
    private $warehouseInterface;

    /**
     * @var Item[]
     */
    private $items = [];

    /**
     * @var Warehouse|null
     */
    private $warehouse;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(
        WarehouseInterface $warehouseInterface,
        SessionInterface $session
    )
    {
        $this->warehouseInterface = $warehouseInterface;
        $this->session = $session;
    }

    public function warehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function hasWarehouse(): bool
    {
        return $this->warehouse !== null;
    }

    public function setWarehouseId(string $warehouseId): void
    {
        $warehouse = new Warehouse();
        $warehouse->setWarehouseId($warehouseId);
        $this->warehouse = $warehouse;
    }

    /**
     * @param Item[] $items
     */
    public function add(array $items): void
    {
        $warehouse = $this->loadWarehouse();
        $this->items = array_merge($this->findAll(), array_map(function (Item $item) use ($warehouse): Item {
            $this->warehouseInterface->add($item, $warehouse);

            return $item;
        }, $items));

        $this->save($warehouse);
    }

    /**
     * @return Item[]
     */
    public function findAll(): array
    {
        $warehouse = $this->loadWarehouse();

        return $this->warehouseInterface->findAll($warehouse);
    }


    /**
     * @param Item[] $items
     */
    public function remove(array $items): void
    {
        $warehouse = $this->loadWarehouse();
        array_walk($items, function (Item $item) use ($warehouse) {
            $this->warehouseInterface->remove($item, $warehouse);
        });

        $this->items = $this->findAll();
        $this->save($warehouse);
    }

    public function destroy(): void
    {
        $warehouse = $this->loadWarehouse();
        $this->warehouseInterface->destroy($warehouse);
        $this->session->remove('basket');
        $this->warehouse = null;
    }

    public function mergeWarehouse(Warehouse $warehouse): void
    {
        $actualWarehouse = $this->warehouse();
        $this->setWarehouseId($warehouse->warehouseId());
        $items = $this->findAll();
        $this->destroy();
        $this->setWarehouseId($actualWarehouse->warehouseId());
        $this->add($items);
    }

    public function total(): float
    {
        return array_sum(array_map(function (Item $item):float {
            return $item->quantity() * $item->price();
        },$this->findAll()));

    }

    private function loadWarehouse(): Warehouse
    {
        if($this->hasWarehouse()){
            $warehouseId = $this->warehouse->warehouseId();
        }elseif ($this->session->has('basket')) {
            $warehouseId = key($this->session->get('basket'));
        } else {
            $warehouseId = Uuid::uuid4()->toString();
        }
        $warehouse = new Warehouse();
        $warehouse->setWarehouseId($warehouseId);

        $this->warehouse = $warehouse;
        return $warehouse;
    }

    private function save(Warehouse $warehouse): void
    {
        $this->session->set('basket', [$warehouse->warehouseId() => $this->items]);
    }


}
