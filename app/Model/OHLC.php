<?php

namespace App\Model;

use Illuminate\Contracts\Support\Arrayable;

class OHLC implements Arrayable
{
    /**
     * @var Coin
     */
    private $coin;

    /**
     * @var string
     */
    private $id;

    /**
     * @var int
     */
    private $open;

    /**
     * @var int
     */
    private $high;

    /**
     * @var int
     */
    private $low;

    /**
     * @var int
     */
    private $close;

    /**
     * @var int
     */
    private $volume;

    /**
     * @var int
     */
    private $marketCap;

    /**
     * @var \DateTime
     */
    private $openDate;

    /**
     * @var \DateTime
     */
    private $closeDate;

    public function getId(): string
    {
        return implode(':', [
            $this->coin,
            $this->openDate->format('Y.m.d.h.i.s'),
            $this->openDate->format('Y.m.d.h.i.s'),
        ]);
    }

    public function toArray()
    {
        return [
            'coin' => $this->getCoin(),
            'open' => $this->getOpen(),
            'high' => $this->getHigh(),
            'low' => $this->getLow(),
            'close' => $this->getClose(),
            'volume' => $this->getVolume(),
            'market_cap' => $this->getMarketCap(),
            'open_date' => $this->getOpenDate()->format('Y-h-d'),
            'close_date' => $this->getCloseDate()->format('Y-m-d'),
        ];
    }

    /**
     * @return Coin
     */
    public function getCoin(): Coin
    {
        return $this->coin;
    }

    /**
     * @param Coin $coin
     * @return OHLC
     */
    public function setCoin(Coin $coin): OHLC
    {
        $this->coin = $coin;
        return $this;
    }

    /**
     * @return int
     */
    public function getOpen(): int
    {
        return $this->open;
    }

    /**
     * @param int $open
     * @return OHLC
     */
    public function setOpen(int $open): OHLC
    {
        $this->open = $open;
        return $this;
    }

    /**
     * @return int
     */
    public function getHigh(): int
    {
        return $this->high;
    }

    /**
     * @param int $high
     * @return OHLC
     */
    public function setHigh(int $high): OHLC
    {
        $this->high = $high;
        return $this;
    }

    /**
     * @return int
     */
    public function getLow(): int
    {
        return $this->low;
    }

    /**
     * @param int $low
     * @return OHLC
     */
    public function setLow(int $low): OHLC
    {
        $this->low = $low;
        return $this;
    }

    /**
     * @return int
     */
    public function getClose(): int
    {
        return $this->close;
    }

    /**
     * @param int $close
     * @return OHLC
     */
    public function setClose(int $close): OHLC
    {
        $this->close = $close;
        return $this;
    }

    /**
     * @return int
     */
    public function getVolume(): int
    {
        return $this->volume;
    }

    /**
     * @param int $volume
     * @return OHLC
     */
    public function setVolume(int $volume): OHLC
    {
        $this->volume = $volume;
        return $this;
    }

    /**
     * @return int
     */
    public function getMarketCap(): int
    {
        return $this->marketCap;
    }

    /**
     * @param int $marketCap
     * @return OHLC
     */
    public function setMarketCap(int $marketCap): OHLC
    {
        $this->marketCap = $marketCap;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getOpenDate(): \DateTime
    {
        return $this->openDate;
    }

    /**
     * @param \DateTime $openDate
     * @return OHLC
     */
    public function setOpenDate(\DateTime $openDate): OHLC
    {
        $this->openDate = $openDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCloseDate(): \DateTime
    {
        return $this->closeDate;
    }

    /**
     * @param \DateTime $closeDate
     * @return OHLC
     */
    public function setCloseDate(\DateTime $closeDate): OHLC
    {
        $this->closeDate = $closeDate;
        return $this;
    }
}