<?php

namespace App\Factory;

use App\Crawler\CoinMarketCap;
use App\Model\Coin;
use Illuminate\Support\Collection;

class OHLCFactory
{
    /**
     * @var CoinMarketCap
     */
    private $crawler;

    /**
     * OHLCFactory constructor.
     * @param CoinMarketCap $crawler
     */
    public function __construct(CoinMarketCap $crawler)
    {
        $this->crawler = $crawler;
    }

    public function build(Coin $coin, \DateTime $from, \DateTime $to): Collection
    {
        return $this->crawler->getData($coin, $from, $to);
    }
}