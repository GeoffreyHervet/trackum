<?php

namespace App\Model;

class OHLC
{
    /**
     * @var Coin
     */
    private $coin;

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

}