<?php

namespace App\Factory;

use App\Model\Coin;

class CoinFactory extends AbstractFactory
{
    public static function create(array $attributes): Coin
    {
        return self::createModel(new Coin(), $attributes);
    }
}
