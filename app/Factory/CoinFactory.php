<?php

namespace App\Factory;

use App\Model\Coin;

class CoinFactory
{
    public static function create(array $attributes): Coin
    {
        $coin = new Coin();

        foreach ($attributes as $key => $value) {
            $coin->{$key} = $value;
        }

        return $coin;
    }
}
