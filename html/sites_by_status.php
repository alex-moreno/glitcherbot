<?php

require_once __DIR__.'/../vendor/autoload.php';

$resultsStorage = new \ScraperBot\Storage\SqlLite3Storage('../glitcherbot.sqlite3');

$compare = [];
if (isset($_GET['date1']) && $_GET['date2']) {
    $compare['date1'] = $_GET['date1'];
    $compare['date2'] = $_GET['date2'];
}

$onlyLatest = NULL;
if (isset($_GET['latest'])) {
    $onlyLatest = $_GET['latest'];
}
$crawls = $resultsStorage->getTimeStamps($compare, $onlyLatest);

$showOnlyNaughty = NULL;
if (isset($_GET['onlynaughty']) && $_GET['onlynaughty'] == true) {
    $showOnlyNaughty = $_GET['onlynaughty'];
}


$tolerance = 1000;
if (isset($_GET['tolerance'])) {
    $tolerance = $_GET['tolerance'];
}

$rows = $secondaryRow = [];
$headers = [];
$index = 0;

$headers[0] = $crawls[0];
$headers[1] = $crawls[1];

// Iterate over the results, preparing columns and rows for the twig template.
$crawlResults[$crawls[0]] = $resultsStorage->getSitesPerStatus($crawls[0], 200);
$crawlResults[$crawls[1]] = $resultsStorage->getSitesPerStatus($crawls[1]);

    // Get the list of results, per site, for a given timestamp and prepare
    // array entries representing the rows.
if (sizeof($crawlResults[$crawls[1]]) > 1) {
        foreach ($crawlResults[$crawls[0]] as $url => $site) {
            if ($crawlResults[$crawls[1]][$site['url']]['statusCode'] != $site['statusCode']) {

                $site_id = $site['url'];
                $site['naughty'] = $crawlResults[$crawls[1]][$site['url']]['naughty'] = '';
                if (empty($rows[$site_id][$index])) {
                    // Prepare the first column.
                    $rows[$site_id][$index] = [];
                    // Prepare the second column.
                    $rows[$site_id][$index+1] = [];
                }

                // Put first results in the first column.
                array_push($rows[$site_id][$index], $site['size'], $site['statusCode'], $site['naughty'], $site['url'], $site['tags']);
                // Put second results in the second column.
                array_push($rows[$site_id][$index+1], $crawlResults[$crawls[1]][$site['url']]['size'],
                    $crawlResults[$crawls[1]][$site['url']]['statusCode'], $crawlResults[$crawls[1]][$site['url']]['naughty'],
                    $crawlResults[$crawls[1]][$site['url']]['url'], $crawlResults[$crawls[1]][$site['url']]['tags']);
            }
    }
}
$index++;

// Specify our Twig templates location
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../src/templates');
// Instantiate our Twig
$twig = new \Twig\Environment($loader);
$template = $twig->load('results_changed_to_errors.twig');
echo $template->render(['headers' => $headers, 'rows' => $rows, 'tolerance' => $tolerance, 'date1' => $compare['date1'], 'date2' => $compare['date2']]);
