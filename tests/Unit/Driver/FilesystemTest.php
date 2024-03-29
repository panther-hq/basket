<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Tests\Unit\Driver;

use League\Flysystem\Filesystem;
use PantherHQ\Basket\Item\Item;
use PantherHQ\Basket\Item\NumericProductId as ProductId;
use PantherHQ\Basket\Item\TextItemId;
use PantherHQ\Basket\Tests\BasketTestCase;
use PantherHQ\Basket\Warehouse;
use PHPUnit\Framework\Assert;

final class FilesystemTest extends BasketTestCase
{
    public function testAddItemToWarehouse(): void
    {
        $warehouse = new Warehouse();
        $warehouse->setWarehouseId('abf8c0a1-c89c-4fde-8087-da87d99754bb');

        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $basket = new \PantherHQ\Basket\Driver\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket->add(new Item(
            new TextItemId($id = 'c06a00d2-4df5-446e-b1a9-6b7528640b27'),
            $productId = new ProductId($productId = 1111),
            $this->faker()->title,
            1,
            9.99,
            new \DateTimeImmutable('now')
        ), $warehouse);

        Assert::assertTrue(file_exists($warehousePath.DIRECTORY_SEPARATOR.$warehouse->warehouseId().DIRECTORY_SEPARATOR.$id));
    }

    public function testRemoveItemFromWarehouse(): void
    {
        $warehouse = new Warehouse();
        $warehouse->setWarehouseId('abf8c0a1-c89c-4fde-8087-da87d99754bb');

        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $basket = new \PantherHQ\Basket\Driver\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket->add($item = new Item(
            new TextItemId($id = '77ac8983-42f7-4cec-960a-f636b92abb06'),
            $productId = new ProductId($productId = 1111),
            $this->faker()->title,
            1,
            9.99,
            new \DateTimeImmutable('now')
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
        $basket = new \PantherHQ\Basket\Driver\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket->add($item = new Item(
            $itemId = new TextItemId('77ac8983-42f7-4cec-960a-f636b92abb06'),
            $productId = new ProductId($productId = 1111),
            $this->faker()->title,
            1,
            9.99,
            new \DateTimeImmutable('now')
        ), $warehouse);

        $basketItem = $basket->getByItemId($itemId, $warehouse);
        Assert::assertSame($item->itemId()->id(), $basketItem->itemId()->id());
        Assert::assertSame($item->price(), $basketItem->price());
        Assert::assertSame($item->quantity(), $basketItem->quantity());
    }

    public function testFindAllFromWarehouse(): void
    {
        $warehouse = new Warehouse();
        $warehouse->setWarehouseId('02d040a2-bdee-4767-858e-e8d333f6a671');

        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $basket = new \PantherHQ\Basket\Driver\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket->add($item = new Item(
            $itemId = new TextItemId('827fd18e-5672-429c-9147-1a16ff6696bf'),
            $productId = new ProductId($productId = 1111),
            $this->faker()->title,
            1,
            9.99,
            new \DateTimeImmutable('now')
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
        $basket = new \PantherHQ\Basket\Driver\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket->add(new Item(
            $itemId = new TextItemId('77ac8983-42f7-4cec-960a-f636b92abb06'),
            $productId = new ProductId($productId = 1111),
            $this->faker()->title,
            1,
            9.99,
            new \DateTimeImmutable('now')
        ), $warehouse);

        $basket->destroy($warehouse);
        Assert::assertFalse(file_exists($warehousePath.DIRECTORY_SEPARATOR.$warehouse->warehouseId()));
    }
}
