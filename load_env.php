<?php

$envFile = __DIR__ . '/.env';
foreach (file($envFile) as $line) {
    $line = array_map('trim', explode('=', trim($line), 2));
    if (count($line) === 2) {
        echo "export {$line[0]}={$line[1]}", PHP_EOL;
    }
}
