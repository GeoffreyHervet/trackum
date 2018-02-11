<?php

namespace App\Factory;

use App\Crawler\CoinMarketCap;
use App\Model\Coin;
use Illuminate\Support\Collection;

class OHLCFactory
{
    public static function build(Coin $coin, \DateTime $from, \DateTime $to): Collection
    {
        return (new CoinMarketCap())->getData($coin, $from, $to);
    }
}