<?php
declare(strict_types=1);

namespace ScraperBot;

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Psr7\Response;
use ScraperBot\Source\SourceInterface;
use ScraperBot\Source\XmlSitemapSource;
use ScraperBot\Storage\StorageInterface;

class Crawler
{

    private $headers = NULL;

    private $storage;

    private $offIndex = 0;

    public function __construct(StorageInterface $storage, $config)
    {
        $this->headers = $config;
        $this->concurrency = $config['concurrency'];

        $this->storage = $storage;
    }

    /**
     * Crawl sites.
     *
     * @param $listOfSites
     * @param $client
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function crawlSites(SourceInterface $source, Client $client, $default_config = NULL, $timestamp = NULL)
    {
        $urls = $source->getLinks();

        // Preparing file to be written.
        $csvManager = new CsvManager();
        $fileToWrite = date('dmY-His') . '-output.csv';

        if (!isset($timestamp)) {
            $timestamp = time();
        }

        $promises = (function () use ($urls, $client, $default_config) {
            foreach ($urls as $url) {
                if (!empty($url)) {
                    // If default config is provided, create a new client each time.
                    if ($default_config != NULL) {
                        $config = $default_config + ['base_uri' => 'http://' . $url];
                        $url = '';
                        $client = new Client($config);
                    }

                    yield $client->getAsync($url, $this->headers);
                }
            }
        })();

        $eachPromise = new EachPromise($promises, [
            // Concurrency to use.
            'concurrency' => $this->concurrency,
            'fulfilled' => function (Response $response, $index) use ($csvManager, $fileToWrite, $timestamp, $urls) {

                $siteCrawled = array();
                $siteCrawled['site_id'] = ($index + 1);
                $siteCrawled['url'] = trim($urls[$index]);
                $siteCrawled['statusCode'] = $response->getStatusCode();
                $body = $response->getBody()->getContents();
                $siteCrawled['size'] = strlen($body);
                $siteCrawled['footprint'] = md5($body);

                $tagDistribution = $this->getTags($body);
                echo PHP_EOL . $siteCrawled['url'] . '-- ';

                $csvManager->writeCsvLine($siteCrawled, $fileToWrite);
                $this->storage->addResult(
                    $siteCrawled['site_id'],
                    $siteCrawled['url'],
                    $siteCrawled['size'],
                    $siteCrawled['statusCode'],
                    $siteCrawled['footprint'],
                    $timestamp
                );
                $this->storage->addTagDistribution($siteCrawled['url'], $tagDistribution, $timestamp);
            },
            'rejected' => function ($reason, $index, $promise) use ($csvManager, $fileToWrite, $timestamp, $urls) {
                // Handle promise rejected here (ie: not existing domains, long timeouts or too many redirects).
                echo 'rejected: ' . $reason . PHP_EOL . ' ----- ';

                $siteCrawled = array();
                $siteCrawled['site_id'] = ($index + 1);
                $siteCrawled['url'] = $urls[$index + 1];
                $siteCrawled['statusCode'] = 'rejected';
                $siteCrawled['size'] = $siteCrawled['footprint'] = 0;
                $csvManager->writeCsvLine(array($index, 'rejected', 0, 0), $fileToWrite);
                $this->storage->addResult(
                    $index,
                    $siteCrawled['url'],
                    0,
                    0,
                    0,
                    $timestamp
                );
            }
        ]);

        $eachPromise->promise()->wait();
    }

    /**
     * Return tag analysis of the given html as string.
     *
     * @param $body
     * @return mixed
     */
    public function getTags($body){
        if ($body != "") {
            $dom = new \DOMDocument();
            $dom->loadHTML($body, LIBXML_NOWARNING | LIBXML_NOERROR);
            $allElements = $dom->getElementsByTagName('*');

            $elementDistribution = [];
            foreach($allElements as $element) {
                if(array_key_exists($element->tagName, $elementDistribution)) {
                    $elementDistribution[$element->tagName] += 1;
                } else {
                    $elementDistribution[$element->tagName] = 1;
                }
            }
            $elementDistribution['total'] = $allElements->length;

            return $elementDistribution;
        }

    }

    /**
     * Crawl sites.
     *
     * @param $listOfSites
     * @param $client
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function crawlSiteMaps(SourceInterface $source, Client $client, $default_config = NULL, $timestamp = NULL, $offIndex = 0)
    {
        echo PHP_EOL . 'Sitemaps crawling>>>> ';

        $this->offIndex = $offIndex;

        if (!isset($timestamp)) {
            $timestamp = time();
        }

        $this->triggerThreadedCrawl($source, $client, $default_config, $offIndex, $timestamp);
    }

    /**
     * Trigger Crawl using threads.
     *
     * @param $urls
     * @param $client
     * @param $default_config
     * @param $offIndex
     * @param $timestamp
     */
    public function triggerThreadedCrawl($source, $client, $default_config, $offIndex, $timestamp) {
        // First read the robots, so we can find the sitemap (if any)
        $urls = $source->getLinks();

        $promises = (function () use ($urls, $client, $default_config, $offIndex) {
            foreach ($urls as $url) {
                // If default config is provided, create a new client each time.
                if ($default_config != NULL) {
                    $config = $default_config + ['base_uri' => 'http://' . $url];
                    $url = '';
                    $client = new Client($config);
                }

                yield $client->getAsync($url . '/robots.txt', $this->headers);
            }
        })();

        $eachPromise = new EachPromise($promises, [
            // Concurrency to use.
            'concurrency' => $this->concurrency,
            'fulfilled' => function (Response $response, $index) use ($timestamp, $urls, $offIndex) {
                foreach(explode(PHP_EOL, $response->getBody()->getContents()) as $line) {
                    // We want to follow Sitemap: urls.
                    if (strpos($line, 'Sitemap:') !== false) {
                        $this->offIndex++;
                        $newurl = trim(substr($line, strlen("Sitemap:"), strlen($line)));

                        // Store new links.
                        $this->storage->addSitemapURL($newurl, $this->offIndex, $timestamp);
                    }
                }

            },
            'rejected' => function ($reason, $index, $promise) use ($timestamp, $urls) {
                // Handle promise rejected here (ie: not existing domains, long timeouts or too many redirects).
                echo 'rejected: ' . $reason . PHP_EOL . ' ----- ';
            }
        ]);

        $eachPromise->promise()->wait();
    }

    public function getListPendingSitemaps($dumpCurrent = TRUE) {
        return $this->storage->getSitemapURLs($dumpCurrent);
    }

    public function getListPendingURL($dumpCurrent = TRUE) {
        return $this->storage->getPendingURLs($dumpCurrent);
    }

    /**
     * Trigger Crawl using threads.
     *
     * @param $urls
     * @param $client
     * @param $default_config
     * @param $offIndex
     * @param $timestamp
     */
    public function extractSitemaps($source, $client, $default_config, $timestamp, $offIndex) {
        // First read the robots, so we can find the sitemap (if any)
        $urls = $source->getLinks();

        $promises = (function () use ($urls, $client, $default_config, $offIndex) {
            foreach ($urls as $url) {
                echo PHP_EOL . 'Found sitemap in:: ' . $url;

                // If default config is provided, create a new client each time.
                if ($default_config != NULL) {
                    $config = $default_config + ['base_uri' => 'http://' . $url];
                    $url = '';
                    $client = new Client($config);
                }

                yield $client->getAsync($url, $this->headers);
            }
        })();

        $eachPromise = new EachPromise($promises, [
            // Concurrency to use.
            'concurrency' => $this->concurrency,
            'fulfilled' => function (Response $response, $index) use ($timestamp, $urls, $offIndex) {
                // We want to follow Sitemap: urls.
                // TODO: NEXT: CRAWL CONTENT OF THE SITEMAP AND INSERT IN THE TEMPORARY URLS:

                $sourceSitemap = new XmlSitemapSource();
                $links = $sourceSitemap->extractLinks($response->getBody()->getContents());
                if(is_array($links)){
                    foreach ($links as $link) {
                        $this->storage->addPendingURL($link, $this->offIndex, $timestamp);
                        $this->offIndex++;
                    }
                }
                else {
                    echo 'not array';
                }
            },
            'rejected' => function ($reason, $index, $promise) use ($timestamp, $urls) {
                // Handle promise rejected here (ie: not existing domains, long timeouts or too many redirects).
                echo 'rejected: ' . $reason . PHP_EOL . ' ----- ';

            }
        ]);

        $eachPromise->promise()->wait();
    }
}
