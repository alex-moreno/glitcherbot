<?php

require_once __DIR__.'/../vendor/autoload.php';

$rows = [];
$headers = [];
$index = 0;

$resultsStorage = new \ScraperBot\Storage\SqlLite3Storage('../glitcherbot.sqlite3');
$crawls = $resultsStorage->getTimeStamps();

$statusCodes = $resultsStorage->getStatusCodes();
foreach ($statusCodes as $code) {
    $headers[$index] = $code;
    $statusResults = $resultsStorage->getStatsByStatus($code);
    foreach ($statusResults as $timestamp => $statusResult) {
        $index++;
        $rows[$timestamp][$code] = $statusResult;
    }

}

// Specify our Twig templates location
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../src/templates');
// Instantiate our Twig
$twig = new \Twig\Environment($loader);
$template = $twig->load('results_status.twig');
echo $template->render(['headers' => $headers, 'rows' => $rows, 'tolerance' => $tolerance]);
