<?php

namespace App\Manager;

use App\Bridge\CoinMarketCap\CoinBridge;
use App\Model\Coin;
use function dump;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use function var_dump;

class CoinManager
{
    /**
     * @var CoinBridge
     */
    private $coinBridge;

    /**
     * CoinManager constructor.
     *
     * @param CoinBridge $coinBridge
     */
    public function __construct(CoinBridge $coinBridge)
    {
        $this->coinBridge = $coinBridge;
    }

    public function getByName(string $name): Coin
    {
        $coin = Coin::where('name', $name)->first();
        if (null === $coin) {
            $coin = $this->checkForUpdate()
                ->first(function (Coin $coin) use ($name): bool {
                    return !strcasecmp($coin->name, $name);
                });
        }

        if ($coin === null) {
            throw new ModelNotFoundException();
        }

        return $coin;
    }

    /**
     * @return Collection|Coin[]
     */
    public function all(): Collection
    {
        return Coin::all();
    }

    /**
     * @return Collection The new coin stored in db
     */
    private function checkForUpdate(): Collection
    {
        $allDbCoins = $this->all();

        return
            Collection::make([$this->coinBridge
            ->getAllCoins()->first()])
            ->filter(function (Coin $newCoin) use ($allDbCoins): bool {
                return !$allDbCoins->contains(function (Coin $dbCoin) use ($newCoin): bool {
                    return $dbCoin->eq($newCoin);
                });
            })
            ->each(function (Coin $newCoin) {
                try {
                    $newCoin->save();
                } catch (QueryException $exception) {
                    // Silent SQL Error (duplicate symbol, e.g: CAT)
                }
            })
        ;
    }


}
