<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class BasketTestCase extends TestCase
{

    /**
     * @var Connection
     */
    protected $connection;

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
        $platform = $this->connection->getDatabasePlatform();

        $this->connection->executeUpdate($platform->getTruncateTableSQL('basket', true /* whether to cascade */));
    }

    protected function tearDown(): void
    {
        $platform = $this->connection->getDatabasePlatform();

        $this->connection->executeUpdate($platform->getTruncateTableSQL('basket', true /* whether to cascade */));
    }

    protected function faker(): Generator
    {
        return Factory::create();
    }
}
