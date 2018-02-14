* Configure local db
* If you want to load easily env without docker, run `./load_env.php`
* Load migration and seed `./artisan migrate:refresh --seed`

To display a coin: `./artisan coin:info Bitcoin`
To display ohlcs of a coin `./artisan ohlc:get Bitcoin --from=2018-02-02 -u`
