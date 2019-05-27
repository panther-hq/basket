<?php

declare(strict_types=1);

namespace Basket;

use Basket\Exception\WarehouseException;
use Basket\Item\Item;
use Basket\Item\ItemId;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;

final class Filesystem implements WarehouseInterface
{
    /**
     * @var \League\Flysystem\Filesystem
     */
    private $filesystem;

    /**
     * @param Config|array $config
     */
    public function __construct(AdapterInterface $adapter, $config = null)
    {
        $this->filesystem = new \League\Flysystem\Filesystem($adapter, $config);
    }

    public function add(Item $item, Warehouse $warehouse): void
    {
        $this->filesystem->put($warehouse->warehouseId().DIRECTORY_SEPARATOR.$item->itemId()->id(), serialize($item));
    }

    public function remove(Item $item, Warehouse $warehouse): void
    {
        if ($this->filesystem->has($warehouse->warehouseId().DIRECTORY_SEPARATOR.$item->itemId()->id())) {
            $this->filesystem->delete($warehouse->warehouseId().DIRECTORY_SEPARATOR.$item->itemId()->id());
        }
    }

    public function getByItemId(ItemId $itemId, Warehouse $warehouse): Item
    {
        if ($this->filesystem->has($warehouse->warehouseId().DIRECTORY_SEPARATOR.$itemId->id())) {
            return unserialize($this->filesystem->read($warehouse->warehouseId().DIRECTORY_SEPARATOR.$itemId->id()));
        }

        throw new WarehouseException(sprintf('Item with item id %s not found in warehouse %s', $itemId->id(), $warehouse->warehouseId()));
    }

    public function findAll(Warehouse $warehouse): array
    {
        if ($this->filesystem->has($warehouse->warehouseId())) {
            $items = $this->filesystem->listContents($warehouse->warehouseId());

            return array_map(function (array $file): Item {
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
