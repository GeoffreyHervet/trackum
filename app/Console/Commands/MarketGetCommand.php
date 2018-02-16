<?php

namespace App\Console\Commands;

use App\Factory\OHLCFactory;
use App\Model\Coin;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MarketGetCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'market:get';

    /**
     * @var string
     */
    protected $description = 'Get the market data';

    /**
     * @var Coin
     */
    private $coin;

    /**
     * @var \DateTime
     */
    private $from;

    /**
     * @var \DateTime|null
     */
    private $to;

    protected function getArguments()
    {
        return [
            ['coin', InputArgument::REQUIRED, 'Which coin (e.g: bitcoin).'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['from', null, InputOption::VALUE_REQUIRED, 'data from.', null],
            ['to', null, InputOption::VALUE_OPTIONAL, 'date to.', null],
        ];
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->coin = new Coin($input->getArgument('coin'));
        $this->from = \DateTime::createFromFormat('Y-m-d', $input->getOption('from'));
        $this->to = \DateTime::createFromFormat('Y-m-d', $input->getOption('to')) ?: new \DateTime();
        if (!$this->from) {
            throw new \RuntimeException(sprintf('Not a valid from date (e.g: %s', date('Y-m-d')));
        }
        $this->from->setTime(0,0,0);
        $this->to->setTime(0,0,0);
    }

    public function handle(OHLCFactory $OHLCFactory)
    {
        $items = $OHLCFactory->build($this->coin, $this->from, $this->to)->toArray();
        if (empty($items)) {
            return;
        }

        $this->table(array_keys($items[0]), $items);
    }
}
