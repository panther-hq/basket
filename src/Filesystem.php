<?php

declare(strict_types=1);

namespace PantherHQ\Basket;

use PantherHQ\Basket\Exception\WarehouseException;
use PantherHQ\Basket\Item\ItemId;
use PantherHQ\Basket\Item\ItemInterface;

final class Filesystem implements WarehouseInterface
{
    /**
     * @var \League\Flysystem\Filesystem
     */
    private $filesystem;

    public function __construct(\League\Flysystem\Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function add(ItemInterface $item, Warehouse $warehouse): void
    {
        $this->filesystem->put($warehouse->warehouseId().DIRECTORY_SEPARATOR.$item->itemId()->id(), serialize($item));
    }

    public function remove(ItemInterface $item, Warehouse $warehouse): void
    {
        if ($this->filesystem->has($warehouse->warehouseId().DIRECTORY_SEPARATOR.$item->itemId()->id())) {
            $this->filesystem->delete($warehouse->warehouseId().DIRECTORY_SEPARATOR.$item->itemId()->id());
        }
    }

    public function getByItemId(ItemId $itemId, Warehouse $warehouse): ItemInterface
    {
        if ($this->filesystem->has($warehouse->warehouseId().DIRECTORY_SEPARATOR.$itemId->id())) {
            return unserialize($this->filesystem->read($warehouse->warehouseId().DIRECTORY_SEPARATOR.$itemId->id()));
        }

        throw new WarehouseException(sprintf('Item with item id %s not found in warehouse %s', $itemId->id(), $warehouse->warehouseId()));
    }

    /**
     * @return ItemInterface[]
     */
    public function findAll(Warehouse $warehouse): array
    {
        if ($this->filesystem->has($warehouse->warehouseId())) {
            $items = $this->filesystem->listContents($warehouse->warehouseId());

            return array_map(function (array $file): ItemInterface {
                return unserialize($this->filesystem->read($file['path']));
            }, $items);
        }

        return [];
    }

    public function destroy(Warehouse $warehouse): void
    {
        if ($this->filesystem->has($warehouse->warehouseId())) {
            $this->filesystem->deleteDir($warehouse->warehouseId());
        }

        return;
    }
}
