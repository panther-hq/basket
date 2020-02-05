<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Tests\Unit\Driver;

use PantherHQ\Basket\Exception\WarehouseException;
use PantherHQ\Basket\Item\Attribute;
use PantherHQ\Basket\Item\Item;
use PantherHQ\Basket\Item\TextItemId;
use PantherHQ\Basket\Item\NumericProductId as ProductId;
use PantherHQ\Basket\Tests\BasketTestCase;
use PantherHQ\Basket\Warehouse;
use PHPUnit\Framework\Assert;

final class DatabaseTest extends BasketTestCase
{
    public function testAddItemToWarehouse(): void
    {
        $warehouse = new Warehouse();
        $warehouse->setWarehouseId('abf8c0a1-c89c-4fde-8087-da87d99754bb');

        $basket = new \PantherHQ\Basket\Driver\Database($this->connection);
        $basket->add(new Item(
            $itemId = new TextItemId($id = 'c06a00d2-4df5-446e-b1a9-6b7528640b27'),
            $productId = new ProductId($productId = 1111),
            $title = $this->faker()->title,
            $quantity = 1,
            $price = 9.99
        ), $warehouse);

        $item = $basket->getByItemId($itemId, $warehouse);
        Assert::assertSame($item->name(), $title);
        Assert::assertSame($item->price(), $price);
        Assert::assertSame($item->quantity(), $quantity);
    }


    /**
     * This tests fails because the
     *
     *
     * @throws WarehouseException
     * @throws \PantherHQ\Basket\Exception\ItemException
     * @throws \Throwable
     */
    public function testAddItemAndThenAddAgainToWarehouse(): void
    {
        $warehouse = new Warehouse();
        $warehouse->setWarehouseId('abf8c0a1-c89c-4fde-8087-da87d99754bb');

        $basket = new \PantherHQ\Basket\Driver\Database($this->connection);
        $item = new Item(
                $itemId = new TextItemId($id = 'c06a00d2-4df5-446e-b1a9-6b7528640b27'),
                $productId = new ProductId($productId = 1111),
                $title = $this->faker()->title,
                $quantity = 1,
                $price = 9.99
        );
        $basket->add($item, $warehouse);
        $itemAgain = new Item(
                $itemId = new TextItemId($id = 'c06a00d2-4df5-446e-b1a9-6b7528640b27'),
                $productId = new ProductId($productId = 1111),
                $title,
                $quantityAgain = 2,
                $price = 9.99
        );
        $basket->add($item, $warehouse);


        $savedItem = $basket->getByItemId($itemId, $warehouse);
        Assert::assertSame($savedItem->name(), $title);
        Assert::assertSame($savedItem->price(), $price);
        // $savedItem->quantity() = 1, should be 3 the item should have been merged cause it's the same freaking ID.
        Assert::assertSame($quantity + $quantityAgain, $savedItem->quantity());
    }

    public function testRemoveItemFromWarehouse(): void
    {
        $warehouse = new Warehouse();
        $warehouse->setWarehouseId('abf8c0a1-c89c-4fde-8087-da87d99754bb');

        $basket = new \PantherHQ\Basket\Driver\Database($this->connection);
        $basket->add($item = new Item(
            $itemId = new TextItemId($id = '77ac8983-42f7-4cec-960a-f636b92abb06'),
            $productId = new ProductId($productId = 1111),
            $this->faker()->title,
            1,
            9.99
        ), $warehouse);
        $basket->remove($item, $warehouse);

        $this->expectException(WarehouseException::class);
        $basket->getByItemId($itemId, $warehouse);
    }

    public function testGetItemByItemIdFromWarehouse(): void
    {
        $warehouse = new Warehouse();
        $warehouse->setWarehouseId('abf8c0a1-c89c-4fde-8087-da87d99754bb');

        $basket = new \PantherHQ\Basket\Driver\Database($this->connection);
        $basket->add($item = new Item(
            $itemId = new TextItemId('77ac8983-42f7-4cec-960a-f636b92abb06'),
            $productId = new ProductId($productId = 1111),
            $this->faker()->title,
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
        $warehouse->setWarehouseId('02d040a2-bdee-4767-858e-e8d333f6a671');

        $basket = new \PantherHQ\Basket\Driver\Database($this->connection);
        $item =
            new Item(
                $itemId = new TextItemId('827fd18e-5672-429c-9147-1a16ff6696bf'),
                $productId = new ProductId($productId = 1111),
                $this->faker()->title,
                1,
                9.99
            );
        $item2 =new Item(
            $itemId = new TextItemId('827fd18e-5672-429c-9147-1a16ff6696bf'),
            $productId = new ProductId($productId = 1111),
            $this->faker()->title,
            1,
            9.99
        );
        $promoAttribute = new Attribute();
        $promoAttribute->setPromotion('test_attribute');
        $item2->setAttribute($promoAttribute);

        $basket->add($item, $warehouse);
        $basket->add($item2, $warehouse);

        $items = $basket->findAll($warehouse);
        Assert::assertCount(2, $basket->findAll($warehouse));

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

        $basket = new \PantherHQ\Basket\Driver\Database($this->connection);
        $basket->add(new Item(
            $itemId = new TextItemId('77ac8983-42f7-4cec-960a-f636b92abb06'),
            $productId = new ProductId($productId = 1111),
            $this->faker()->title,
            1,
            9.99
        ), $warehouse);

        $basket->destroy($warehouse);

        $this->expectException(WarehouseException::class);
        $basket->getByItemId($itemId, $warehouse);
    }

}
