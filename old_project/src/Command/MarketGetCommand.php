<?php

namespace App\Command;

use App\Model\Coin;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MarketGetCommand extends Command
{
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

    protected function configure()
    {
        $this
            ->setName('markets:get')
            ->setDescription('Get market data from a coin')
            ->addArgument('coin', InputArgument::REQUIRED, 'Which coin (e.g: bitcoin)')
            ->addOption('from', 'f', InputOption::VALUE_REQUIRED, 'date from')
            ->addOption('to',  't', InputOption::VALUE_OPTIONAL, 'date to')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->coin = new Coin($input->getArgument('coin'));
        $this->from = \DateTime::createFromFormat('Y-m-d', $input->getOption('from'));
        $this->to = \DateTime::createFromFormat('Y-m-d', $input->getOption('to')) ?: null;

        if (!$this->from) {
            throw new \RuntimeException(sprintf('Not a valid from date (e.g: %s', date('Y-m-d')));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }


}
