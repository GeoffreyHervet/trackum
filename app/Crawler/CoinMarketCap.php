<?php

namespace App\Crawler;

use App\Model\Coin;
use App\Model\OHLC;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;

class CoinMarketCap
{
    private static $urlScheme = 'https://coinmarketcap.com/currencies/%s/historical-data/?start=%s&end=%s';
    private static $dateFormat = 'Ymd';

    protected function getUrl(Coin $coin, \DateTime $from, \DateTime $to): string
    {
        return sprintf(
            static::$urlScheme,
            $coin,
            $from->format(static::$dateFormat),
            $to->format(static::$dateFormat)
        );
    }

    public function getData(Coin $coin, \DateTime $from, \DateTime $to): Collection
    {
        $pageContent = $this->getPageContent($coin, $from, $to);

        $crawler = new Crawler($pageContent);
        $nodes = $crawler->filter('#historical-data table tbody tr')->extract(['_text']);

        return Collection::make($nodes)
            ->map(function (string $nodeContent) use ($coin): OHLC {
                return $this->nodeContentToOHLC($coin, $nodeContent);
            });
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
                return str_replace(',','', $value);
            })
            ->values();

        $openDate = new \DateTime($node[0]);
        $openDate->setTime(0, 0, 0);
        $closeDate = clone $openDate;
        $closeDate->setTime(23, 59, 59);

        return (new OHLC())
            ->setOpenDate($openDate)
            ->setCloseDate($closeDate)
            ->setCoin($coin)
            ->setOpen($node[1] * 100)
            ->setHigh($node[2] * 100)
            ->setLow($node[3] * 100)
            ->setClose($node[4] * 100)
            ->setVolume((int) $node[5])
            ->setMarketCap((int) $node[6]);
    }

    private function getPageContent(Coin $coin, \DateTime $from, \DateTime $to): string
    {
        // To remove after debug phase
        $fileName = 'cache/'.implode('.', [$coin,$from->format('Ymd'),$to->format('Ymd'),'html']);
        if (file_exists($fileName)) return file_get_contents($fileName);
        // !To remove

        $client = new \GuzzleHttp\Client();
        $url = $this->getUrl($coin, $from, $to);

        $response = $client->get($url);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException(sprintf(
                'Status code is %d for %s [%s %s]',
                $response->getStatusCode(),
                $coin,
                $from->format('Y-m-d H:i'),
                $from->format('Y-m-d H:i')
            ));
        }

        $bodyContent = $response->getBody()->getContents();
        // To remove after debug phase
//        file_put_contents($fileName, $bodyContent);
         // !To remove

        return $bodyContent;
    }
}