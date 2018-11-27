<?php

namespace App\Factory;

use App\Model\Coin;
use App\Model\OHLC;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psy\Exception\RuntimeException;

class OHLCFactory extends AbstractFactory
{
    public static function create(array $attributes): OHLC
    {
        static::validCoinAttribute($attributes);

        $coin = $attributes['coin'];
        unset($attributes['coin']);

        return static::createWithCoin($coin, $attributes);
    }

    public static function createWithCoin(Coin $coin, array $attributes): OHLC
    {
        /** @var OHLC $ohlc */
        $ohlc = self::createModel(new OHLC(), $attributes);
        $ohlc->coin()->associate($coin);

        return $ohlc;
    }

    private static function validCoinAttribute(array $attributes)
    {
        if (!isset($attributes['coin'])) {
            throw new ModelNotFoundException('No coin attribute given to '.__CLASS__);
        }
        if (!($attributes['coin'] instanceof Coin)) {
            throw new RuntimeException('No coin attribute given to '.__CLASS__);
        }
    }
}
