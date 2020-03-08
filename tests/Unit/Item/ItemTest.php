<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Tests\Unit\Item;

use PantherHQ\Basket\Exception\ItemException;
use PantherHQ\Basket\Item\Attribute;
use PantherHQ\Basket\Item\Item;
use PantherHQ\Basket\Item\NumericItemId;
use PantherHQ\Basket\Item\NumericProductId;
use PantherHQ\Basket\Item\TextItemId;
use PantherHQ\Basket\Tests\BasketTestCase;
use PHPUnit\Framework\Assert;

final class ItemTest extends BasketTestCase
{
    public function testCreateItemWithNumericItemId(): void
    {
        $item = new Item(
            new NumericItemId(1),
            new NumericProductId($productId = 1111),
            $this->faker()->title,
            1,
            9.99,
            new \DateTimeImmutable('now')
        );

        Assert::assertSame(1, $item->itemId()->id());
        Assert::assertSame(1, $item->quantity());
        Assert::assertSame(9.99, $item->price());
    }

    public function testCreateItemWithTextItemId(): void
    {
        $item = new Item(
            new TextItemId('3009062a-6679-4d17-a51c-507679f24e8b'),
            new NumericProductId($productId = 1111),
            $this->faker()->title,
            1,
            9.99,
            new \DateTimeImmutable('now')
        );

        Assert::assertSame('3009062a-6679-4d17-a51c-507679f24e8b', $item->itemId()->id());
        Assert::assertSame(1, $item->quantity());
        Assert::assertSame(9.99, $item->price());
    }

    public function testQuantityException(): void
    {
        $this->expectException(ItemException::class);
        new Item(
            new TextItemId('3009062a-6679-4d17-a51c-507679f24e8b'),
            new NumericProductId($productId = 1111),
            $this->faker()->title,
            0,
            9.99,
            new \DateTimeImmutable('now')
        );
    }

    public function testPriceException(): void
    {
        $this->expectException(ItemException::class);
        new Item(
            new TextItemId('3009062a-6679-4d17-a51c-507679f24e8b'),
            new NumericProductId($productId = 1111),
            $this->faker()->title,
            1,
            0.00,
            new \DateTimeImmutable('now')
        );
    }

    public function testToJson(): void
    {
        $item = new Item(
            new TextItemId($id = '3009062a-6679-4d17-a51c-507679f24e8b'),
            new NumericProductId($productId = 1111),
            $title = $this->faker()->title,
            $quantity = 1,
            $price = 1.99,
            $addedAt = new \DateTimeImmutable('now')
        );
        $attribute = (new Attribute())
            ->setDescription($description = $this->faker()->text)
            ->setPromotion($promotion = $this->faker()->jobTitle);
        $item->setAttribute($attribute);

        $data = json_decode((string)json_encode($item), true);
        Assert::assertSame($id, $data['itemId']);
        Assert::assertSame($productId, $data['productId']);
        Assert::assertSame($title, $data['name']);
        Assert::assertSame($quantity, $data['quantity']);
        Assert::assertSame($price, $data['price']);
        Assert::assertSame($addedAt->getTimestamp(), $data['addedAt']);
        Assert::assertSame($promotion, $data['attribute']['promotion']);
        Assert::assertSame($description, $data['attribute']['description']);
    }
}
