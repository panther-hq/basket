<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Tests\Unit;

use Faker\Factory;
use League\Flysystem\Filesystem;
use PantherHQ\Basket\Item\Attribute;
use PantherHQ\Basket\Item\Item;
use PantherHQ\Basket\Item\NumericItemId;
use PantherHQ\Basket\Item\NumericProductId;
use PantherHQ\Basket\Item\TextItemId;
use PantherHQ\Basket\Tests\BasketTestCase;
use PantherHQ\Basket\Warehouse;
use PHPUnit\Framework\Assert;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

final class BasketTest extends BasketTestCase
{
    public function testAddItemsToBasket(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \PantherHQ\Basket\Driver\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket = new \PantherHQ\Basket\Basket(
            $warehouseInterface,
            $session = new Session(new MockArraySessionStorage()),
            'basket'
        );

        $items = [];
        for ($i = 0; $i < 5; $i++) {
            $items[] = new Item(
                new TextItemId(Uuid::uuid4()->toString()),
                new NumericProductId(123123),
                $this->faker()->title,
                random_int(1, 10),
                random_int(1, 100),
                new \DateTimeImmutable('now')
            );
        }

        $basket->add($items);
        Assert::assertContains($items, $session->get('basket'));
        Assert::assertCount(count($items), current($session->get('basket')));
    }

    public function testAddItemsToBasketDatabase(): void
    {
        $warehouseInterface = new \PantherHQ\Basket\Driver\Database($this->connection);
        $basket = new \PantherHQ\Basket\Basket(
            $warehouseInterface,
            $session = new Session(new MockArraySessionStorage()),
            'basket'
        );

        $items = [];
        for ($i = 0; $i < 5; $i++) {
            $items[] = new Item(
                new TextItemId(Uuid::uuid4()->toString()),
                new NumericProductId(111),
                $this->faker()->title,
                random_int(1, 10),
                random_int(1, 100),
                new \DateTimeImmutable('now')
            );
        }

        $basket->add($items);
        Assert::assertContains($items, $session->get('basket'));
        Assert::assertCount(count($items), current($session->get('basket')));
        Assert::assertCount(count($items), $basket->findAll());
    }

    public function testAddItemsToBasketInDifferentInstances(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \PantherHQ\Basket\Driver\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket = new \PantherHQ\Basket\Basket(
            $warehouseInterface,
            $session = new Session(new MockArraySessionStorage()),
            'basket'
        );

        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(
                new TextItemId(Uuid::uuid4()->toString()),
                $productId = new NumericProductId($productId = 1111),
                $this->faker()->title,
                random_int(1, 10),
                random_int(1, 100),
                new \DateTimeImmutable('now')
            );
        }

        $itemsWarehouse = $items;
        $basket->add($items);

        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(
                new TextItemId(Uuid::uuid4()->toString()),
                new NumericProductId($productId = 1111),
                $this->faker()->title,
                random_int(1, 10),
                random_int(1, 100),
                new \DateTimeImmutable('now')
            );
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
        $warehouseInterface = new \PantherHQ\Basket\Driver\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket = new \PantherHQ\Basket\Basket(
            $warehouseInterface,
            $session = new Session(new MockArraySessionStorage()),
            'basket'
        );

        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(
                new TextItemId(Uuid::uuid4()->toString()),
                new NumericProductId($productId = 1111),
                $this->faker()->title,
                random_int(1, 10),
                random_int(1, 100),
                new \DateTimeImmutable('now')
            );
        }
        $itemsWarehouse = $items;
        $basket->add($items);

        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(
                new TextItemId(Uuid::uuid4()->toString()),
                new NumericProductId($productId = 1111),
                $this->faker()->title,
                random_int(1, 10),
                random_int(1, 100),
                new \DateTimeImmutable('now')
            );
        }

        $basket->add($items);

        $itemsWarehouse = array_merge($itemsWarehouse, $items);

        $items = $basket->findAll();
        Assert::assertCount(count($itemsWarehouse), $items);
    }

    public function testDestroyBasket(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \PantherHQ\Basket\Driver\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket = new \PantherHQ\Basket\Basket(
            $warehouseInterface,
            $session = new Session(new MockArraySessionStorage()),
            'basket'
        );

        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(
                new TextItemId(Uuid::uuid4()->toString()),
                new NumericProductId($productId = 1111),
                $this->faker()->title,
                random_int(1, 10),
                random_int(1, 100),
                new \DateTimeImmutable('now')
            );
        }
        $basket->add($items);
        $basket->destroy();

        Assert::assertFalse($basket->hasWarehouse());
        Assert::assertFalse($session->has('basket'));
    }

    public function testRemoveItemFromBasket(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \PantherHQ\Basket\Driver\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket = new \PantherHQ\Basket\Basket(
            $warehouseInterface,
            $session = new Session(new MockArraySessionStorage()),
            'basket'
        );

        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(
                new TextItemId(Uuid::uuid4()->toString()),
                new NumericProductId($productId = 1111),
                $this->faker()->title,
                random_int(1, 10),
                random_int(1, 100),
                new \DateTimeImmutable('now')
            );
        }
        $basket->add($items);

        /** @var Item[] $removeItems */
        $removeItems = [current($items), end($items)];
        $basket->remove($removeItems);

        Assert::assertCount(count($basket->findAll()), current($session->get('basket')));
    }

    public function testAddOwnWarehouse(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \PantherHQ\Basket\Driver\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket = new \PantherHQ\Basket\Basket(
            $warehouseInterface,
            $session = new Session(new MockArraySessionStorage()),
            'basket'
        );
        $faker = Factory::create();
        $basket->setWarehouseId($faker->email);
        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(
                new TextItemId(Uuid::uuid4()->toString()),
                new NumericProductId($productId = 1111),
                $this->faker()->title,
                random_int(1, 10),
                random_int(1, 100),
                new \DateTimeImmutable('now')
            );
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
        $warehouseInterface = new \PantherHQ\Basket\Driver\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket = new \PantherHQ\Basket\Basket(
            $warehouseInterface,
            $session = new Session(new MockArraySessionStorage()),
            'basket'
        );
        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(
                new TextItemId(Uuid::uuid4()->toString()),
                new NumericProductId($productId = 1111),
                $this->faker()->title,
                random_int(1, 10),
                random_int(1, 100),
                new \DateTimeImmutable('now')
            );
        }
        $basket->add($items);

        $total = array_sum(array_map(function (Item $item): float {
            return $item->quantity() * $item->price();
        },
                                   $items));
        Assert::assertSame($basket->total(), $total);
    }

    public function testCountOnBasket(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \PantherHQ\Basket\Driver\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $basket = new \PantherHQ\Basket\Basket(
            $warehouseInterface,
            $session = new Session(new MockArraySessionStorage()),
            'basket'
        );
        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(
                new TextItemId(Uuid::uuid4()->toString()),
                new NumericProductId($productId = 1111),
                $this->faker()->title,
                random_int(1, 10),
                random_int(1, 100),
                new \DateTimeImmutable('now')
            );
        }
        $basket->add($items);

        $count = (int) array_sum(array_map(function (Item $item): float {
            return $item->quantity();
        },
                                         $items));
        Assert::assertSame($basket->count(), $count);
    }

    public function testMergeWarehouse(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \PantherHQ\Basket\Driver\Filesystem(new Filesystem(new \League\Flysystem\Adapter\Local($warehousePath)));
        $faker = Factory::create();

        $basketGuest = new \PantherHQ\Basket\Basket(
            $warehouseInterface,
            $session = new Session(new MockArraySessionStorage()),
            'basket'
        );
        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(
                new TextItemId(Uuid::uuid4()->toString()),
                new NumericProductId($productId = 1111),
                $this->faker()->title,
                random_int(1, 10),
                random_int(1, 100),
                new \DateTimeImmutable('now')
            );
        }
        $itemsWarehouseGuest = $items;
        $basketGuest->add($items);
        Assert::assertCount(count($basketGuest->findAll()), $items);

        $basketAuth = new \PantherHQ\Basket\Basket(
            $warehouseInterface,
            $session = new Session(new MockArraySessionStorage()),
            'basket'
        );
        $basketAuth->setWarehouseId($faker->email);
        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(
                new TextItemId(Uuid::uuid4()->toString()),
                new NumericProductId($productId = 1111),
                $this->faker()->title,
                random_int(1, 10),
                random_int(1, 100),
                new \DateTimeImmutable('now')
            );
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

    public function testMergeWarehouseDatabase(): void
    {
        $warehouseInterface = new \PantherHQ\Basket\Driver\Database($this->connection);
        $faker = Factory::create();

        $basketGuest = new \PantherHQ\Basket\Basket(
            $warehouseInterface,
            $session = new Session(new MockArraySessionStorage()),
            'basket'
        );
        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(
                new TextItemId(Uuid::uuid4()->toString()),
                new NumericProductId($productId = 1111),
                $this->faker()->title,
                random_int(1, 10),
                random_int(1, 100),
                new \DateTimeImmutable('now')
            );
        }
        $itemsWarehouseGuest = $items;
        $basketGuest->add($items);
        Assert::assertCount(count($basketGuest->findAll()), $items);

        $basketAuth = new \PantherHQ\Basket\Basket(
            $warehouseInterface,
            $session = new Session(new MockArraySessionStorage()),
            'basket'
        );
        $basketAuth->setWarehouseId($faker->email);
        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(
                new TextItemId(Uuid::uuid4()->toString()),
                new NumericProductId($productId = 1111),
                $this->faker()->title,
                random_int(1, 10),
                random_int(1, 100),
                new \DateTimeImmutable('now')
            );
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

    public function testAddTwoSameItemsButOneIsPromotionToBasket(): void
    {
        $warehouseInterface = new \PantherHQ\Basket\Driver\Database($this->connection);

        $basket = new \PantherHQ\Basket\Basket(
            $warehouseInterface,
            $session = new Session(new MockArraySessionStorage()),
            'basket'
        );

        $items = [];
        $name = $this->faker()->title;
        $items[] = new Item(
            new NumericItemId(1),
            new NumericProductId($productId = 1111),
            $name,
            1,
            10,
            new \DateTimeImmutable('now')
        );

        $attribute = new Attribute();
        $attribute->setPromotion('promotion_1');
        $item = new Item(
            new NumericItemId(1),
            new NumericProductId($productId = 1111),
            $name,
            1,
            5,
            new \DateTimeImmutable('now')
        );
        $item->setAttribute($attribute);
        $items[] = $item;

        $basket->add($items);
        Assert::assertContains($items, $session->get('basket'));
        Assert::assertCount(count($items), current($session->get('basket')));
    }
}
