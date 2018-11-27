<?php

namespace App\Bridge\CoinMarketCap;

use App\Factory\OHLCFactory;
use App\Model\Coin;
use App\Model\OHLC;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;

class OHLCBridge
{
    private static $cacheTTL = 3600 * 2;
    private static $urlScheme = 'https://coinmarketcap.com/currencies/%s/historical-data/?start=%s&end=%s';
    private static $dateFormat = 'Ymd';

    /**
     * @var Repository
     */
    private $cache;

    /**
     * CoinMarketCap constructor.
     *
     * @param Repository $cache
     */
    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    public static function getMinPeriod(): \DateInterval
    {
        return new \DateInterval('P1D');
    }

    public function getByCoinBetween(Coin $coin, \DateTime $from, \DateTime $to, bool $checkForUpdate = false): Collection
    {
        $pageContent = $this->getPageContent($coin, $from, $to);

        $crawler = new Crawler($pageContent);
        $nodes = $crawler->filter('#historical-data table tbody tr')->extract(['_text']);

        return Collection::make($nodes)
            ->map(function (string $nodeContent) use ($coin): OHLC {
                return $this->nodeContentToOHLC($coin, $nodeContent);
            });
    }

    public function canUpdate(Collection $ohlcCollection, \DateTime $from, \DateTime $to): bool
    {
        return null !== $this->createPeriodRange($from, $to)
            ->map(function (\DateTime $dateTime): int {
                return (int) $dateTime->format('Ymd');
            })
            // Returns the 1st date which is not in $ohlcCollection
            ->first(function (int $date) use ($ohlcCollection): bool {
                return null === $ohlcCollection->first(function (OHLC $OHLC) use ($date): bool {
                    return (int) $OHLC->openDate->format('Ymd') === $date;
                });
            }, null);
    }

    protected function getUrl(Coin $coin, \DateTime $from, \DateTime $to): string
    {
        return sprintf(
            static::$urlScheme,
            $coin->slug,
            $from->format(static::$dateFormat),
            $to->format(static::$dateFormat)
        );
    }

    private function nodeContentToOHLC(Coin $coin, string $nodeContent): OHLC
    {
        $node = Collection::make(explode(PHP_EOL, $nodeContent))
            ->map(function (string $value): string {
                return trim($value);
            })
            ->filter(function (string $value): bool {
                return !empty($value);
            })
            ->map(function (string $value): string {
                return str_replace(',', '', $value);
            })
            ->values();

        $openDate = new \DateTime($node[0]);
        $openDate->setTime(0, 0, 0);
        $closeDate = clone $openDate;
        $closeDate->setTime(23, 59, 59);

        return OHLCFactory::create([
            'openDate' => $openDate,
            'closeDate' => $closeDate,
            'coin' => $coin,
            'open' => $node[1] * 100,
            'high' => $node[2] * 100,
            'low' => $node[3] * 100,
            'close' => $node[4] * 100,
            'volume' => (int) $node[5],
            'marketCap' => (int) $node[6],
        ]);
    }

    private function getPageContent(Coin $coin, \DateTime $from, \DateTime $to): string
    {
        $url = $this->getUrl($coin, $from, $to);
        if ($this->cache->has($url)) {
            return $this->cache->get($url);
        }

        $client = new \GuzzleHttp\Client();
        $response = $client->get($url);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException(sprintf(
                'Status code is %d for %s [%s %s]',
                $response->getStatusCode(),
                $coin,
                $from->format('Y-m-d H:i'),
                $from->format('Y-m-d H:i')
            ));
        }

        $bodyContent = $response->getBody()->getContents();
        $this->cache->set($url, $bodyContent, static::$cacheTTL);

        return $bodyContent;
    }

    private function createPeriodRange(\DateTime $from, \DateTime $to): Collection
    {
        return Collection::make(new \DatePeriod($from, static::getMinPeriod(), $to));
    }
}
