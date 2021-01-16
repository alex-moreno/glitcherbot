<?php

require_once __DIR__.'/../vendor/autoload.php';

$resultsStorage = new \ScraperBot\Storage\SqlLite3Storage('../glitcherbot.sqlite3');

$compare = [];
if (isset($_GET['date1']) && $_GET['date2']) {
    echo $_GET['date1'] . ' date2: ' . $_GET['date2'];
    $compare['date1'] = $_GET['date1'];
    $compare['date2'] = $_GET['date2'];
}

if (isset($_GET['latest'])) {
    $onlyLatest = $_GET['latest'];
}
$crawls = $resultsStorage->getTimeStamps($compare, $onlyLatest);

if (isset($_GET['onlynaughty']) && $_GET['onlynaughty'] == 1) {
    $showOnlyNaughty = $_GET['onlynaughty'];
}


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
    $naughtySites = $resultsStorage->getNaughtySites($crawls[$lastElem-1], $crawls[$lastElem], $tolerance, $onlyLatest);
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
            $site_id = $site['url'];
            $site['naughty'] = '';

            if (isset($naughtySites[$site_id]) && sizeof($crawls) > 1) {
                $site['naughty'] = 'naughty';
            }

            if ($showOnlyNaughty == TRUE && $site['naughty'] == 'naughty' || $showOnlyNaughty != TRUE) {
                if (empty($rows[$site_id][$index])) {
                    $rows[$site_id][$index] = [];
                }

                array_push($rows[$site_id][$index], $site['size'], $site['statusCode'], $site['naughty'], $site['url'], $site['tags']);
            }
        }
    }

    $index++;
}

// Specify our Twig templates location
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../src/templates');
// Instantiate our Twig
$twig = new \Twig\Environment($loader);
$template = $twig->load('results.twig');
echo $template->render(['headers' => $headers, 'rows' => $rows, 'tolerance' => $tolerance, 'date1' => $compare['date1'], 'date2' => $compare['date2']]);
