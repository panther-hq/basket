<?php

declare(strict_types=1);

namespace PantherHQ\Basket;

use PantherHQ\Basket\Exception\WarehouseException;
use PantherHQ\Basket\Item\ItemInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class Basket implements BasketInterface
{
    /**
     * @var WarehouseInterface
     */
    private $warehouseInterface;

    /**
     * @var ItemInterface[]
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
    ) {
        $this->warehouseInterface = $warehouseInterface;
        $this->session = $session;
    }

    public function warehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function hasWarehouse(): bool
    {
        return $this->warehouse instanceof Warehouse;
    }

    public function setWarehouseId(string $warehouseId): void
    {
        $warehouse = new Warehouse();
        $warehouse->setWarehouseId($warehouseId);
        $this->warehouse = $warehouse;
    }

    /**
     * @param ItemInterface[] $items
     */
    public function add(array $items): void
    {
        $warehouse = $this->loadWarehouse();
        $this->items = array_merge($this->findAll(), array_map(function (ItemInterface $item) use ($warehouse): ItemInterface {
            $this->warehouseInterface->add($item, $warehouse);

            return $item;
        }, $items));

        $this->save($warehouse);
    }

    /**
     * @return ItemInterface[]
     */
    public function findAll(): array
    {
        $warehouse = $this->loadWarehouse();

        return $this->warehouseInterface->findAll($warehouse);
    }

    /**
     * @param ItemInterface[] $items
     */
    public function remove(array $items): void
    {
        $warehouse = $this->loadWarehouse();
        array_walk($items, function (ItemInterface $item) use ($warehouse): void {
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
        if (!$actualWarehouse instanceof Warehouse) {
            throw new WarehouseException(sprintf('Warehouse is not exists'));
        }
        $this->setWarehouseId($warehouse->warehouseId());
        $items = $this->findAll();
        $this->destroy();
        $this->setWarehouseId($actualWarehouse->warehouseId());
        $this->add($items);
    }

    public function total(): float
    {
        return array_sum(array_map(function (ItemInterface $item): float {
            return $item->quantity() * $item->price();
        }, $this->findAll()));
    }

    public function count(): int
    {
        return (int) array_sum(array_map(function (ItemInterface $item): int {
            return $item->quantity();
        }, $this->findAll()));
    }

    private function loadWarehouse(): Warehouse
    {
        if ($this->warehouse() instanceof Warehouse) {
            $warehouseId = $this->warehouse()->warehouseId();
        } elseif ($this->session->has('basket')) {
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
