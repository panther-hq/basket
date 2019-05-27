<?php

declare(strict_types=1);

namespace Basket\Tests\Unit;

use Basket\Item\Item;
use Basket\Item\TextItemId;
use Basket\Warehouse;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class FilesystemTest extends TestCase
{
    public function testAddItemToWarehouse(): void
    {
        $warehouse = new Warehouse();
        $warehouse->setWarehouseId('abf8c0a1-c89c-4fde-8087-da87d99754bb');

        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $basket = new \Basket\Filesystem(new \League\Flysystem\Adapter\Local($warehousePath));
        $basket->add(new Item(
            new TextItemId($id = 'c06a00d2-4df5-446e-b1a9-6b7528640b27'),
            1,
            9.99
        ), $warehouse);

        Assert::assertTrue(file_exists($warehousePath.DIRECTORY_SEPARATOR.$warehouse->warehouseId().DIRECTORY_SEPARATOR.$id));
    }

    public function testRemoveItemFromWarehouse(): void
    {
        $warehouse = new Warehouse();
        $warehouse->setWarehouseId('abf8c0a1-c89c-4fde-8087-da87d99754bb');

        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $basket = new \Basket\Filesystem(new \League\Flysystem\Adapter\Local($warehousePath));
        $basket->add($item = new Item(
            new TextItemId($id = '77ac8983-42f7-4cec-960a-f636b92abb06'),
            1,
            9.99
        ), $warehouse);

        Assert::assertTrue(file_exists($warehousePath.DIRECTORY_SEPARATOR.$warehouse->warehouseId().DIRECTORY_SEPARATOR.$id));

        $basket->remove($item, $warehouse);

        Assert::assertFalse(file_exists($warehousePath.DIRECTORY_SEPARATOR.$warehouse->warehouseId().DIRECTORY_SEPARATOR.$id));
    }

    public function testGetItemByItemIdFromWarehouse(): void
    {
        $warehouse = new Warehouse();
        $warehouse->setWarehouseId('abf8c0a1-c89c-4fde-8087-da87d99754bb');

        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $basket = new \Basket\Filesystem(new \League\Flysystem\Adapter\Local($warehousePath));
        $basket->add($item = new Item(
            $itemId = new TextItemId('77ac8983-42f7-4cec-960a-f636b92abb06'),
            1,
            9.99
        ), $warehouse);

        $basketItem = $basket->getByItemId($itemId, $warehouse);
        Assert::assertSame($item->itemId()->id(), $basketItem->itemId()->id());
        Assert::assertSame($item->price(), $basketItem->price());
        Assert::assertSame($item->quantity(), $basketItem->quantity());
    }

    public function testFindAllFromWarehouse(): void
    {
        $warehouse = new Warehouse();
        $warehouse->setWarehouseId('abf8c0a1-c89c-4fde-8087-da87d99754bb');

        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $basket = new \Basket\Filesystem(new \League\Flysystem\Adapter\Local($warehousePath));
        $basket->add($item = new Item(
            $itemId = new TextItemId('77ac8983-42f7-4cec-960a-f636b92abb06'),
            1,
            9.99
        ), $warehouse);

        $items = $basket->findAll($warehouse);
        /** @var Item $basketItem */
        foreach ($items as $basketItem) {
            Assert::assertSame($item->itemId()->id(), $basketItem->itemId()->id());
            Assert::assertSame($item->price(), $basketItem->price());
            Assert::assertSame($item->quantity(), $basketItem->quantity());
        }
    }

    public function testDestroyWarehouse(): void
    {
        $warehouse = new Warehouse();
        $warehouse->setWarehouseId('abf8c0a1-c89c-4fde-8087-da87d99754bb');

        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $basket = new \Basket\Filesystem(new \League\Flysystem\Adapter\Local($warehousePath));
        $basket->add(new Item(
            $itemId = new TextItemId('77ac8983-42f7-4cec-960a-f636b92abb06'),
            1,
            9.99
        ), $warehouse);

        $basket->destroy($warehouse);
        Assert::assertFalse(file_exists($warehousePath.DIRECTORY_SEPARATOR.$warehouse->warehouseId()));
    }
}
