<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Item;

final class NumericItemId implements ItemId
{
    /**
     * @var int
     */
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function id(): int
    {
        return $this->id;
    }
}
