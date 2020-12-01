<?php
declare(strict_types=1);

namespace Crawler;

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use ScrapperBot\Crawler;

echo $argv[1];
if (!empty($argv[1]) && file_exists($argv[1])) {
    $sitesinCSV = $argv[1];
} else {
  // Default csv.
  echo "File not found: Please provide a valid csv file to load the urls from";
  exit;
}

// HTTP Client.
$client = new Client(['defaults' => [
    'verify' => false
]]);

$headers = include('config.php');
$crawler = new Crawler($headers);
echo PHP_EOL . 'Starting crawling. Date: ' . date('l jS \of F Y h:i:s A');
$crawler->crawlSites($sitesinCSV, $client);
echo PHP_EOL . 'Crawling finished at: ' . date('l jS \of F Y h:i:s A');
