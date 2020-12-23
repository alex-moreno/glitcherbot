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
                    echo PHP_EOL . 'querying: ' . $url;

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
                echo PHP_EOL . 'Code: ' . $response->getStatusCode();
                echo ' index: ' . ($index + 1);

                $siteCrawled = array();
                $siteCrawled['site_id'] = ($index + 1);
                $siteCrawled['url'] = $urls[$index];
                $siteCrawled['statusCode'] = $response->getStatusCode();
                $body = $response->getBody()->getContents();
                $siteCrawled['size'] = strlen($body);
                $siteCrawled['footprint'] = md5($body);

                $csvManager->writeCsvLine($siteCrawled, $fileToWrite);
                $this->storage->addResult(
                    $siteCrawled['site_id'],
                    $siteCrawled['url'],
                    $siteCrawled['size'],
                    $siteCrawled['statusCode'],
                    $siteCrawled['footprint'],
                    $timestamp
                );
            },
            'rejected' => function ($reason, $index, $promise) use ($csvManager, $fileToWrite, $timestamp, $urls) {
                // Handle promise rejected here (ie: not existing domains, long timeouts or too many redirects).
                echo 'rejected: ' . $reason;

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
     * Crawl sites.
     *
     * @param $listOfSites
     * @param $client
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function crawlSiteMaps(SourceInterface $source, Client $client, $default_config = NULL, $timestamp = NULL, $offIndex = 0)
    {
        echo 'calling here>>>> ';

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

        echo 'urls:::';
        print_r($urls);

        $promises = (function () use ($urls, $client, $default_config, $offIndex) {
            foreach ($urls as $url) {
                echo 'idexing:: ' . $url;

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
                        echo PHP_EOL . 'Storing sitemap: ' . $newurl . PHP_EOL;
                        echo 'offindex: ' . $this->offIndex . PHP_EOL;
                        // Store new links.
                        $this->storage->addTemporaryURL($newurl, $this->offIndex, $timestamp);
                    }
                }

            },
            'rejected' => function ($reason, $index, $promise) use ($timestamp, $urls) {
                // Handle promise rejected here (ie: not existing domains, long timeouts or too many redirects).
                echo 'rejected: ' . $reason;
            }
        ]);

        $eachPromise->promise()->wait();
    }

    public function getListPendingSitemaps() {
        return $this->storage->getTemporaryURLs();
    }
}
