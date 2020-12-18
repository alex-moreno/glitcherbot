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

// TODO: fix to latest two crawls.
// TODO: add a form to select crawls.
$rows = $resultsStorage->getCrawlDiffs($crawls[0], $crawls[1], $tolerance);

// Iterate over the results, preparing columns and rows for the twig template.
foreach ($crawls as $timestamp) {
    // Get site crawl results for each timestamp.
    $headers[$index] = $timestamp;

    $index++;
}


// Specify our Twig templates location
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../src/templates');
// Instantiate our Twig
$twig = new \Twig\Environment($loader);
$template = $twig->load('crawls_diffs.twig');
echo $template->render(['headers' => $headers, 'rows' => $rows, 'tolerance' => $tolerance]);
