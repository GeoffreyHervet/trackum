<?php

namespace App\Model;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

class Coin extends Model implements Arrayable
{
    protected $table = 'coins';
    protected $fillable = [
        'name',
        'symbol',
        'slug',
    ];

    public $timestamps = true;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $symbol;

    /**
     * @var string
     */
    protected $slug;

    public function ohlc()
    {
        return $this->hasMany(OHLC::class);
    }
}
