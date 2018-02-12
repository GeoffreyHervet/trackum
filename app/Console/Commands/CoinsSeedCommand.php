<?php

namespace App\Console\Commands;


use App\Bridge\CoinMarketCap\CoinBridge;
use App\Factory\OHLCFactory;
use App\Model\Coin;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use \Illuminate\Support\Facades\DB;

class CoinsSeedCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'coins:seed';

    /**
     * @var string
     */
    protected $description = 'Seed coins from coin market cap';

    public function handle(CoinBridge $coinMarketCap)
    {
        $coinMarketCap->getAllCoins()->dd();
    }
}
