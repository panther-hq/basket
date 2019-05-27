<?php

declare(strict_types=1);

namespace Basket\Tests\Unit;

use Basket\Item\Item;
use Basket\Item\TextItemId;
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
        $warehouseInterface = new \Basket\Filesystem(new \League\Flysystem\Adapter\Local($warehousePath));
        $basket = new \Basket\Basket($warehouseInterface, $session = new Session(new MockArraySessionStorage()));

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
        $warehouseInterface = new \Basket\Filesystem(new \League\Flysystem\Adapter\Local($warehousePath));
        $basket = new \Basket\Basket($warehouseInterface, $session = new Session(new MockArraySessionStorage()));

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
            Assert::assertTrue(in_array($item, current($session->get('basket')), false));
        }
        Assert::assertCount(count($itemsWarehouse), current($session->get('basket')));
    }

    public function testFindAllFromBasket(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \Basket\Filesystem(new \League\Flysystem\Adapter\Local($warehousePath));
        $basket = new \Basket\Basket($warehouseInterface, $session = new Session(new MockArraySessionStorage()));

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
        foreach ($items as $item) {
            Assert::assertTrue(in_array($item, $itemsWarehouse, false));
        }
        Assert::assertCount(count($itemsWarehouse), $items);
    }

    public function testDestroyBasket(): void
    {
        $warehousePath = getcwd().DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'var';
        $warehouseInterface = new \Basket\Filesystem(new \League\Flysystem\Adapter\Local($warehousePath));
        $basket = new \Basket\Basket($warehouseInterface, $session = new Session(new MockArraySessionStorage()));

        $items = [];
        for ($i = 0; $i < 4; $i++) {
            $items[] = new Item(new TextItemId(Uuid::uuid4()->toString()), random_int(1, 10), random_int(1, 100));
        }
        $basket->add($items);

        $basket->destroy();

        Assert::assertFalse($basket->hasWarehouse());
        Assert::assertFalse($session->has('basket'));
    }
}
