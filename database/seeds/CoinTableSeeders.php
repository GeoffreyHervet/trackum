<?php

use App\Manager\CoinManager;
use App\Model\Coin;
use Illuminate\Database\Seeder;

class CoinTableSeeders extends Seeder
{
    /**
     * @var CoinManager
     */
    private $coinManager;

    public function __construct(CoinManager $coinManager)
    {
        $this->coinManager = $coinManager;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()

    {
        $this->coinManager->checkForUpdate()
            ->each(function (Coin $coin) {
                echo '[+] ', $coin->name , ' (', $coin->symbol ,')', PHP_EOL;
            });
    }
}
