<?php
declare(strict_types=1);


namespace PantherHQ\Basket\Driver;


final class DatabaseConnection
{

    /**
     * @var string
     */
    private $databaseName;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $driver;

    public function __construct(
        string $databaseName,
        string $user,
        string $password,
        string $host,
        int $port,
        string $driver
    )
    {
        $this->databaseName = $databaseName;
        $this->user = $user;
        $this->password = $password;
        $this->host = $host;
        $this->driver = $driver;
        $this->port = $port;
    }

    public function databaseName(): string
    {
        return $this->databaseName;
    }

    public function user(): string
    {
        return $this->user;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function host(): string
    {
        return $this->host;
    }

    public function port(): int
    {
        return $this->port;
    }

    public function driver(): string
    {
        return $this->driver;
    }


}