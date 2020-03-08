<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Driver;

use PantherHQ\Basket\Item\ItemInterface;

abstract class DriverAbstract
{
    protected function generateItemName(ItemInterface $item): string
    {
        $name = $item->itemId()->id();
        if ($item->hasAttribute() && $item->attribute()->hasPromotion()) {
            $name = sprintf('%s_%s', $item->itemId()->id(), $item->attribute()->promotion());
        }

        return (string) $name;
    }
}
