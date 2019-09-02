<?php
declare(strict_types=1);


namespace PantherHQ\Basket\Tests\Unit\Driver;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use PantherHQ\Basket\Driver\DatabaseConnection;
use PantherHQ\Basket\Item\Item;
use PantherHQ\Basket\Item\TextItemId;
use PantherHQ\Basket\Tests\BasketTestCase;
use PantherHQ\Basket\Warehouse;

final class DatabaseTest extends BasketTestCase
{

    /**
     * @var Connection
     */
    private $connection;
    protected function setUp(): void
    {

        $this->connection = DriverManager::getConnection([
            'dbname' => 'basket',
            'user' => 'travis',
            'password' => '',
            'host' => '127.0.0.1',
            'driver' => 'pdo_mysql',
            'port' => 3306,
        ]);
    }

    public function testAddItemToWarehouse(): void
    {
        $warehouse = new Warehouse();
        $warehouse->setWarehouseId('abf8c0a1-c89c-4fde-8087-da87d99754bb');

        $basket = new \PantherHQ\Basket\Driver\Database($this->connection);
        $basket->add(new Item(
            $itemId = new TextItemId($id = 'c06a00d2-4df5-446e-b1a9-6b7528640b27'),
            $title = $this->faker()->title,
            $quantity = 1,
            $price = 9.99
        ), $warehouse);

        $item = $basket->getByItemId($itemId, $warehouse);
        $this->assertSame($item->name() ,$title);
        $this->assertSame($item->price() ,$price);
        $this->assertSame($item->quantity() ,$quantity);
    }

}