<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Coin extends Model
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

}
