<?php

namespace App\Console\Commands;

use App\Manager\CoinManager;
use App\Model\Coin;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CoinCommand extends Command
{
    /**
     * @var string
     */
    private $coinName;

    /**
     * @var string
     */
    protected $name = 'coin:info';

    /**
     * @var string
     */
    protected $description = 'Seed coins from coin market cap';

    public function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of coin you want to display.'],
        ];
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->coinName = $input->getArgument('name');
    }

    public function handle(CoinManager $coinManager)
    {
        $coin = $coinManager->getByName($this->coinName);
        $coinArray = $coin->toArray();

        $this->table(array_keys($coinArray), [$coinArray]);
    }
}
