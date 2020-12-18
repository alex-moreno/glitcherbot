<?php
declare(strict_types=1);

namespace ScraperBot;

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Psr7\Response;
use ScraperBot\Source\SourceInterface;
use ScraperBot\Storage\StorageInterface;

class Crawler {

    private $headers = NULL;

    private $storage;

    public function __construct(StorageInterface $storage, $config) {
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
    public function crawlSites(SourceInterface $source, Client $client, $default_config = NULL) {
        $urls = $source->getLinks();

        // Preparing file to be written.
        $csvManager = new CsvManager();
        $fileToWrite = date('dmY-His') . '-output.csv';

        $timestamp = time();

        $promises = (function () use ($urls, $client, $default_config) {
            foreach ($urls as $url) {
                // don't forget using generator
                echo PHP_EOL . 'querying: ' . $url;

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
            // how many concurrency we are use
            'concurrency' => $this->concurrency,
            'fulfilled' => function (Response $response, $index) use ($csvManager, $fileToWrite, $timestamp) {
                echo PHP_EOL . 'Code: ' . $response->getStatusCode();
                echo ' index: ' . ($index + 1);

                $siteCrawled = Array();
                $siteCrawled['url'] = ($index + 1);
                $siteCrawled['statusCode'] = $response->getStatusCode();
                $body = $response->getBody()->getContents();
                $siteCrawled['size'] = strlen($body);
                $siteCrawled['footprint'] = md5($body);

                $csvManager->writeCsvLine($siteCrawled,$fileToWrite);
                $this->storage->addResult(
                    $siteCrawled['url'],
                    $siteCrawled['url'],
                    $siteCrawled['size'],
                    $siteCrawled['statusCode'],
                    $siteCrawled['footprint'],
                    $timestamp
                );
            },
            'rejected' => function ($reason, $index, $promise) use ($csvManager, $fileToWrite, $timestamp) {
                // Handle promise rejected here (ie: not existing domains, long timeouts or too many redirects).
                echo 'rejected: ' . $reason;

                $siteCrawled = Array();
                $siteCrawled['url'] = $index;
                $siteCrawled['statusCode'] = 'rejected';
                $siteCrawled['size'] = $siteCrawled['footprint'] = 0;
                $csvManager->writeCsvLine(array($index, 'rejected',0, 0),$fileToWrite);
                $this->storage->addResult(
                    $index,
                    $index,
                    0,
                    0,
                    0,
                    $timestamp
                );
            }
        ]);

        $eachPromise->promise()->wait();
    }
}

