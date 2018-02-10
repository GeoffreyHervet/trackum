<?php

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use App\Command\MarketGetCommand;

$app = new Application('Cashalot');

$app->add(new MarketGetCommand());

$app->run();