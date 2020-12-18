<?php

require_once __DIR__.'/../vendor/autoload.php';

$resultsStorage = new \ScraperBot\Storage\SqlLite3Storage('../railerdb.sqlite3');
$crawls = $resultsStorage->getTimeStamps();

$rows = [];
$headers = [];
$index = 0;

$tolerance = 1000;
if (isset($_GET['tolerance'])) {
    $tolerance = $_GET['tolerance'];
}

// We just use the last two crawls.
if (sizeof($crawls) > 1) {
    $lastElem = array_key_last($crawls);
    $naughtySites = $resultsStorage->getCrawlDiffs($crawls[$lastElem-1], $crawls[$lastElem], $tolerance);
}

// Iterate over the results, preparing columns and rows for the twig template.
foreach ($crawls as $timestamp) {
    // Get site crawl results for each timestamp.
    $resultsByTimestamp = $resultsStorage->getResultsbyTimestamp($timestamp);
    $headers[$index] = $timestamp;

    // Get the list of results, per site, for a given timestamp and prepare
    // array entries representing the rows.
    foreach ($resultsByTimestamp as $listOfSites) {
        foreach ($listOfSites as $site) {
            $site_id = $site['site_id'];
            if (isset($naughtySites[$site_id]) && sizeof($crawls) > 1) {
                $site['naughty'] = 'naughty';
            }

            // Initialise the row for the site if it's empty.
            if (empty($rows[$site_id][$index])) {
                $rows[$site_id][$index] = [];
            }

            array_push($rows[$site_id][$index], $site['size'], $site['statusCode'],$site['naughty']);

        }
    }

    $index++;
}

// Populate missing data for the rows and sort by index to maintain
// column order.
foreach ($rows as $site_id => $row) {
    for ($i = 0; $i < $index; $i++) {
        if (empty($rows[$site_id][$i])) {
            $rows[$site_id][$i] = ['', ''];
        }
    }
    ksort($rows[$site_id]);
}

// Specify our Twig templates location
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../src/templates');
// Instantiate our Twig
$twig = new \Twig\Environment($loader);
$template = $twig->load('results.twig');
echo $template->render(['headers' => $headers, 'rows' => $rows]);
