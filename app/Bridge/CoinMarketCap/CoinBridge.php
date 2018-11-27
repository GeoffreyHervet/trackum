<?php

namespace App\Bridge\CoinMarketCap;

use App\Factory\CoinFactory;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Cache\Repository as Cache;
use Symfony\Component\DomCrawler\Crawler;

class CoinBridge
{
    private static $url = 'https://coinmarketcap.com/all/views/all/';
    private static $slugRegex = '#^/currencies/([\w-]+)/$#i';
    private static $cacheTTL = 3600 * 24;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * CoinMarketCap constructor.
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function getAllCoins(): Collection
    {
        $crawler = new Crawler($this->getPageData());
        $nodes = $crawler->filter('#currencies-all tbody tr');

        return Collection::make($nodes)
            ->map(function (\DOMElement $nodeContent) {
                return $this->nodeContentToCoin($nodeContent);
            })
            ->filter();
    }

    private function nodeContentToCoin(\DOMElement $element)
    {
        $crawler = (new Crawler($element))->filter('.currency-name, .currency-symbol');

        $symbol = trim($crawler->filter('.currency-symbol')->text());
        $link = $crawler->filter('.currency-symbol a')->attr('href');
        preg_match(static::$slugRegex, $link, $matches);
        $slug = $matches[1];
        $name = $crawler->filter('.currency-name-container')->text();

        return CoinFactory::create([
            'name' => $name,
            'slug' => $slug,
            'symbol' => $symbol,
        ]);
    }

    private function getPageData(): string
    {
        if ($this->cache->has(static::$url)) {
            return $this->cache->get(static::$url);
        }

        $client = new \GuzzleHttp\Client();
        $response = $client->get(static::$url);
        $bodyResponse = $response->getBody()->getContents();
        $this->cache->set(static::$url, $bodyResponse, static::$cacheTTL);

        return $bodyResponse;
    }
}
