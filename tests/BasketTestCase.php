<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Tests;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class BasketTestCase extends TestCase
{
    protected function faker(): Generator
    {
        return Factory::create();
    }
}
