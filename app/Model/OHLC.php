<?php

namespace App\Model;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

class OHLC extends Model implements Arrayable
{
    protected $table = 'ohlcs';
    public $timestamps = false;

    protected $dates = [
        'openDate',
        'closeDate',
    ];

    protected $fillable = [
//        'coin',
        'open',
        'high',
        'low',
        'close',
        'volume',
        'marketCap',
        'openDate',
        'closeDate',
    ];

    /**
     * @var Coin
     */
//    protected $coin;

    /**
     * @var int
     */
    protected $open;

    /**
     * @var int
     */
    protected $high;

    /**
     * @var int
     */
    protected $low;

    /**
     * @var int
     */
    protected $close;

    /**
     * @var int
     */
    protected $volume;

    /**
     * @var int
     */
    protected $marketCap;

    /**
     * @var \DateTime
     */
    protected $openDate;

    /**
     * @var \DateTime
     */
    protected $closeDate;

    public static function sortByOpenDate(): callable
    {
        return function (OHLC $a, OHLC $b): OHLC {
            return $a->openDate->getTimestamp() < $b->openDate->getTimestamp()
                ? $a
                : $b;
        };
    }

    public function coin()
    {
        return $this->belongsTo(Coin::class);
    }
}
