<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Item;

final class TextProductId implements ItemId
{
    /**
     * @var string
     */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function id(): string
    {
        return $this->id;
    }
}
