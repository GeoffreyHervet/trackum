<?php

namespace App\Manager;

use App\Bridge\CoinMarketCap\OHLCBridge;
use App\Model\Coin;
use App\Model\OHLC;
use Illuminate\Support\Collection;

class OHLCManager
{
    /**
     * @var OHLCBridge
     */
    private $ohlcBridge;

    /**
     * OHLCManager constructor.
     *
     * @param OHLCBridge $ohlcBridge
     */
    public function __construct(OHLCBridge $ohlcBridge)
    {
        $this->ohlcBridge = $ohlcBridge;
    }

    public function findByCoin(Coin $coin): Collection
    {
        return OHLC::where('coin', $coin)->all();
    }

    public function findByCoinSince(Coin $coin, \DateTime $from, bool $checkForUpdate = false): Collection
    {
        $items = OHLC::where([
                ['coin_id', '=', $coin->id],
                ['openDate', '>=', $from],
            ])
            ->orderBy('openDate', 'ASC')
            ->get();

        if (!$checkForUpdate) {
            return $items;
        }

        return $this->checkForUpdates($coin, $from, new \DateTime(), $items)
            ->concat($items)
            ->sortBy('openDate');
    }

    public function findByCoinBetween(
        Coin $coin,
        \DateTime $from,
        \DateTime $to,
        bool $checkForUpdate = false
    ): Collection {
        $items = OHLC::where([
                ['coin_id', '=', $coin->id],
                ['openDate', '>=', $from],
                ['closeDate', '<=', $to],
            ])
            ->orderBy('openDate', 'ASC')
            ->get()
        ;

        if (!$checkForUpdate) {
            return $items;
        }

        return $this->checkForUpdates($coin, $from, $to, $items)
            ->concat($items)
            ->sortBy('openDate');
    }

    private function checkForUpdates(Coin $coin, \DateTime $from, \DateTime $to, Collection $dbOHLCs = null): Collection
    {
        if (null !== $dbOHLCs && !$this->ohlcBridge->canUpdate($dbOHLCs, $from, $to)) {
            return $dbOHLCs;
        }
        if (null === $dbOHLCs) {
            $dbOHLCs = $this->findByCoinBetween($coin, $from, $to, false);
        }

        return $this->ohlcBridge
            ->getByCoinBetween($coin, $from, $to)
            // Remove ohlc already stored
            ->filter(function (OHLC $OHLC) use ($dbOHLCs): bool {
                return null === $dbOHLCs->first(function ($dbOHLC) use ($OHLC): bool {
                    return $dbOHLC->openDate->getTimestamp() === $OHLC->openDate->getTimestamp();
                });
            })
            ->each(function (OHLC $OHLC) {
                $OHLC->save();
            });
    }
}
