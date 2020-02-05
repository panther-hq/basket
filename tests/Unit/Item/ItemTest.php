<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Tests\Unit\Item;

use PantherHQ\Basket\Exception\ItemException;
use PantherHQ\Basket\Item\Item;
use PantherHQ\Basket\Item\NumericProductId;
use PantherHQ\Basket\Item\NumericItemId;
use PantherHQ\Basket\Item\TextItemId;
use PantherHQ\Basket\Tests\BasketTestCase;
use PHPUnit\Framework\Assert;

final class ItemTest extends BasketTestCase {
    public function testCreateItemWithNumericItemId(): void {
        $item = new Item(new NumericItemId(1),
                         new NumericProductId($productId = 1111),
                         $this->faker()->title,
                         1,
                         9.99
        );

        Assert::assertSame(1, $item->itemId()->id());
        Assert::assertSame(1, $item->quantity());
        Assert::assertSame(9.99, $item->price());
    }

    public function testCreateItemWithTextItemId(): void {
        $item = new Item(
                new TextItemId('3009062a-6679-4d17-a51c-507679f24e8b'),
                new NumericProductId($productId = 1111),
                $this->faker()->title,
                1,
                9.99
        );

        Assert::assertSame('3009062a-6679-4d17-a51c-507679f24e8b', $item->itemId()->id());
        Assert::assertSame(1, $item->quantity());
        Assert::assertSame(9.99, $item->price());
    }

    public function testQuantityException(): void {
        $this->expectException(ItemException::class);
        new Item(
                new TextItemId('3009062a-6679-4d17-a51c-507679f24e8b'),
                new NumericProductId($productId = 1111),
                $this->faker()->title,
                0,
                9.99
        );
    }

    public function testPriceException(): void {
        $this->expectException(ItemException::class);
        new Item(
                new TextItemId('3009062a-6679-4d17-a51c-507679f24e8b'),
                new NumericProductId($productId = 1111),
                $this->faker()->title,
                1,
                0.00
        );
    }
}
