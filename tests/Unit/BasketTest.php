<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Tests\Unit;

use Faker\Factory;
use League\Flysystem\Filesystem;
use PantherHQ\Basket\Item\Item;
use PantherHQ\Basket\Item\TextItemId;
use PantherHQ\Basket\Warehouse;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

final class BasketTest extends TestCase
{
    public function testAddItemsToBasket(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \PantherHQ\Basket\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket = new \PantherHQ\Basket\Basket($warehouseInterface, $session = new Session(new MockArraySessionStorage()));

        $items = [];
        for ($i = 0; $i < 5; $i++) {
            $items[] = new Item(new TextItemId(Uuid::uuid4()->toString()), random_int(1, 10), random_int(1, 100));
        }

        $basket->add($items);
        Assert::assertContains($items, $session->get('basket'));
        Assert::assertCount(count($items), current($session->get('basket')));
    }

    public function testAddItemsToBasketInDifferentInstances(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \PantherHQ\Basket\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket = new \PantherHQ\Basket\Basket($warehouseInterface, $session = new Session(new MockArraySessionStorage()));

        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(new TextItemId(Uuid::uuid4()->toString()), random_int(1, 10), random_int(1, 100));
        }

        $itemsWarehouse = $items;
        $basket->add($items);

        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(new TextItemId(Uuid::uuid4()->toString()), random_int(1, 10), random_int(1, 100));
        }
        $itemsWarehouse = array_merge($itemsWarehouse, $items);
        $basket->add($items);

        foreach ($items as $item) {
            Assert::assertTrue(in_array($item, current($session->get('basket')), true));
        }
        Assert::assertCount(count($itemsWarehouse), current($session->get('basket')));
    }

    public function testFindAllFromBasket(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \PantherHQ\Basket\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket = new \PantherHQ\Basket\Basket($warehouseInterface, $session = new Session(new MockArraySessionStorage()));

        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(new TextItemId(Uuid::uuid4()->toString()), random_int(1, 10), random_int(1, 100));
        }
        $itemsWarehouse = $items;
        $basket->add($items);

        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(new TextItemId(Uuid::uuid4()->toString()), random_int(1, 10), random_int(1, 100));
        }

        $basket->add($items);

        $itemsWarehouse = array_merge($itemsWarehouse, $items);

        $items = $basket->findAll();
        Assert::assertCount(count($itemsWarehouse), $items);
    }

    public function testDestroyBasket(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \PantherHQ\Basket\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket = new \PantherHQ\Basket\Basket($warehouseInterface, $session = new Session(new MockArraySessionStorage()));

        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(new TextItemId(Uuid::uuid4()->toString()), random_int(1, 10), random_int(1, 100));
        }
        $basket->add($items);

        $basket->destroy();

        Assert::assertFalse($basket->hasWarehouse());
        Assert::assertFalse($session->has('basket'));
    }

    public function testRemoveItemFromBasket(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \PantherHQ\Basket\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket = new \PantherHQ\Basket\Basket($warehouseInterface, $session = new Session(new MockArraySessionStorage()));

        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(new TextItemId(Uuid::uuid4()->toString()), random_int(1, 10), random_int(1, 100));
        }
        $basket->add($items);

        /** @var Item[] $removeItems */
        $removeItems = [
            current($items),
            end($items),
        ];
        $basket->remove($removeItems);

        Assert::assertCount(count($basket->findAll()), current($session->get('basket')));
    }

    public function testAddOwnWarehouse(): void
    {
        $faker = Factory::create();
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \PantherHQ\Basket\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket = new \PantherHQ\Basket\Basket($warehouseInterface, $session = new Session(new MockArraySessionStorage()));
        $basket->setWarehouseId($faker->email);
        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(new TextItemId(Uuid::uuid4()->toString()), random_int(1, 10), random_int(1, 100));
        }
        $basket->add($items);
        $itemsWarehouse = $basket->findAll();
        Assert::assertCount(count($itemsWarehouse), $items);

        $basket->destroy();
        Assert::assertFalse($basket->hasWarehouse());
        Assert::assertFalse($session->has('basket'));
    }

    public function testTotalOnBasket(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \PantherHQ\Basket\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket = new \PantherHQ\Basket\Basket($warehouseInterface, $session = new Session(new MockArraySessionStorage()));
        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(new TextItemId(Uuid::uuid4()->toString()), random_int(1, 10), random_int(1, 100));
        }
        $basket->add($items);

        $total = array_sum(array_map(function (Item $item): float {
            return $item->quantity() * $item->price();
        }, $items));
        Assert::assertSame($basket->total(), $total);
    }

    public function testCountOnBasket(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \PantherHQ\Basket\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket = new \PantherHQ\Basket\Basket($warehouseInterface, $session = new Session(new MockArraySessionStorage()));
        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(new TextItemId(Uuid::uuid4()->toString()), random_int(1, 10), random_int(1, 100));
        }
        $basket->add($items);

        $count = (int) array_sum(array_map(function (Item $item): float {
            return $item->quantity();
        }, $items));
        Assert::assertSame($basket->count(), $count);
    }

    public function testMergeWarehouse(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \PantherHQ\Basket\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $faker = Factory::create();

        $basketGuest = new \PantherHQ\Basket\Basket($warehouseInterface, $session = new Session(new MockArraySessionStorage()));
        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(new TextItemId(Uuid::uuid4()->toString()), random_int(1, 10), random_int(1, 100));
        }
        $itemsWarehouseGuest = $items;
        $basketGuest->add($items);
        Assert::assertCount(count($basketGuest->findAll()), $items);

        $basketAuth = new \PantherHQ\Basket\Basket($warehouseInterface, $session = new Session(new MockArraySessionStorage()));
        $basketAuth->setWarehouseId($faker->email);
        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(new TextItemId(Uuid::uuid4()->toString()), random_int(1, 10), random_int(1, 100));
        }
        $itemsWarehouseAuth = $items;
        $basketAuth->add($items);
        Assert::assertCount(count($basketGuest->findAll()), $items);

        $itemsWarehouse = array_merge($itemsWarehouseGuest, $itemsWarehouseAuth);

        if ($basketGuest->warehouse() instanceof Warehouse) {
            $basketAuth->mergeWarehouse($basketGuest->warehouse());
        }

        Assert::assertCount(count($basketAuth->findAll()), $itemsWarehouse);
    }
}
