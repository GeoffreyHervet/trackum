<?php

namespace App\Console\Commands;

use App\Manager\CoinManager;
use App\Manager\OHLCManager;
use App\Model\Coin;
use App\Model\OHLC;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OHLCGetCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'ohlc:get';

    /**
     * @var string
     */
    protected $description = 'Get the market data';

    /**
     * @var Coin
     */
    private $coin;

    /**
     * @var bool
     */
    private $checkForUpdate;

    /**
     * @var \DateTime
     */
    private $from;

    /**
     * @var \DateTime|null
     */
    private $to;

    /**
     * @var CoinManager
     */
    private $coinManager;

    /**
     * @var OHLCManager
     */
    private $ohlcManager;

    /**
     * OHLCGetCommand constructor.
     *
     * @param CoinManager $coinManager
     * @param OHLCManager $ohlcManager
     */
    public function __construct(CoinManager $coinManager, OHLCManager $ohlcManager)
    {
        $this->coinManager = $coinManager;
        $this->ohlcManager = $ohlcManager;
        parent::__construct();
    }

    protected function getArguments()
    {
        return [
            ['coin', InputArgument::REQUIRED, 'Which coin (e.g: bitcoin).'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['update', 'u', InputOption::VALUE_NONE, 'check for update', null],
            ['from', null, InputOption::VALUE_REQUIRED, 'data from.', null],
            ['to', null, InputOption::VALUE_OPTIONAL, 'date to.', null],
        ];
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->coin = $this->coinManager->getByName($input->getArgument('coin') ?: '');
        $this->checkForUpdate = $input->getOption('update');
        $this->from = \DateTime::createFromFormat('Y-m-d', $input->getOption('from'));
        $this->to = \DateTime::createFromFormat('Y-m-d', $input->getOption('to')) ?: null;

        if (!$this->from) {
            throw new \RuntimeException(sprintf('Not a valid from date (e.g: %s).', date('Y-m-d')));
        }
        $this->from->setTime(0, 0, 0);
        if ($this->to) {
            $this->to->setTime(0, 0, 0);
        }
    }

    public function handle()
    {
        $coinArray = $this->coin->toArray();
        $this->table(array_keys($coinArray), [$coinArray]);

        $ohlcCollection = null === $this->to
            ? $this->ohlcManager->findByCoinSince($this->coin, $this->from, $this->checkForUpdate)
            : $this->ohlcManager->findByCoinBetween($this->coin, $this->from, $this->to, $this->checkForUpdate);

        $this->displayOHLC($ohlcCollection);
    }

    private function displayOHLC(Collection $OHLCCollection)
    {
        if ($OHLCCollection->isEmpty()) {
            return;
        }

        $ohlcs = $OHLCCollection->map(function (OHLC $OHLC): array {
            return $this->format($OHLC);
        });

        $headers = array_keys($ohlcs[0]);
        $this->table($headers, $ohlcs);
    }

    private function format(OHLC $OHLC): array
    {
        $amountFormat = '$ %9s';
        $hugeAmountFormat = '$ %9s';

        return [
            'id' => $OHLC->id,
            'open_date' => $OHLC->openDate->format('Y-m-d'),
            'open' => sprintf($amountFormat, number_format($OHLC->open / 100, 2)),
            'high' => sprintf($amountFormat, number_format($OHLC->high / 100, 2)),
            'low' => sprintf($amountFormat, number_format($OHLC->low / 100, 2)),
            'close' => sprintf($amountFormat, number_format($OHLC->close / 100, 2)),
            'close_date' => $OHLC->openDate->format('Y-m-d'),
            'market_cap' => sprintf($hugeAmountFormat, number_format($OHLC->marketCap / 100, 2)),
            'volume' => sprintf($hugeAmountFormat, number_format($OHLC->volume / 100, 2)),
            '% volume' => sprintf('%5.2f', 100 * $OHLC->volume / $OHLC->marketCap),
        ];
    }
}
