<?php
declare(strict_types=1);

namespace ScrapperBot;

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Psr7\Response;

class Crawler {
    private $headers = NULL;

    public function __construct($config) {
        $this->headers = $config;
        $this->concurrency = $config['concurrency'];
    }

    /**
     * Crawl sites.
     *
     * @param $listOfSites
     * @param $client
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function crawlSites($listOfSites, Client $client, $default_config = NULL) {
        // Preparing file to be written.
        $csvManager = new \csvManager();
        $fileToWrite = date('dmY-His') . '-output.csv';

        $promises = (function () use ($listOfSites, $client, $default_config) {
            foreach ($listOfSites as $site) {
                $url = $site;

                // If default config is provided, create a new client each time.
                if ($default_config != NULL) {
                    $config = $default_config + ['base_uri' => 'http://' . $url];
                    $client = new Client($config);
                }

                // don't forget using generator
                echo PHP_EOL . 'querying: ' . $url;
                yield $client->getAsync($url, $this->headers);

            }
        })();

        $eachPromise = new EachPromise($promises, [
            // how many concurrency we are use
            'concurrency' => $this->concurrency,
            'fulfilled' => function (Response $response, $index) use ($csvManager, $fileToWrite) {
                echo PHP_EOL . 'Code: ' . $response->getStatusCode();
                echo ' index: ' . ($index + 1);

                $siteCrawled = Array();
                $siteCrawled['url'] = ($index + 1);
                $siteCrawled['statusCode'] = $response->getStatusCode();
                $body = $response->getBody()->getContents();
                $siteCrawled['size'] = strlen($body);
                $siteCrawled['footprint'] = md5($body);
                $csvManager->writeCsvLine($siteCrawled,$fileToWrite);
            },
            'rejected' => function ($reason, $index, $promise) use ($csvManager, $fileToWrite) {
                // Handle promise rejected here (ie: not existing domains, long timeouts or too many redirects).
                echo 'rejected: ' . $reason;

                $siteCrawled = Array();
                $siteCrawled['url'] = $index;
                $siteCrawled['statusCode'] = 'rejected';
                $siteCrawled['size'] = $siteCrawled['footprint'] = 0;
                $csvManager->writeCsvLine(array($index, 'rejected',0, 0),$fileToWrite);
            }
        ]);

        $eachPromise->promise()->wait();
    }

    /**
     * Request to return the size of a given site.
     *
     * @param $client
     * @param $site
     * @return callable
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function syncRequest($client, $site) {

        try {
            // TODO: move the headers to a settings file.
            // Load headers from a file.
            $res = $client->request('GET', $site, $this->headers);

            // Return the size of the response body.
            $htmlSize = strlen($res->getBody()->getContents());
            $htmlFootprint = md5($res->getBody()->getContents());
            return array($htmlFootprint, $htmlSize, $res->getStatusCode());
        } catch (Exception $exception) {
            echo "Unrecoverable Exception happened in $site" . PHP_EOL;

            return array('', 500);
        }

    }

}

