<?php

use App\Model\Coin;

$factory->define(Coin::class, function () {
    return [
        'name' => 'bitcoin',
        'symbol' => 'btc',
    ];
});
