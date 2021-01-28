<?php
require_once __DIR__.'/../vendor/autoload.php';

$resultsStorage = new \ScraperBot\Storage\SqlLite3Storage('../glitcherbot.sqlite3');

$options = $resultsStorage->getTimeStamps();
$codes = [];
$dataset1 = [];
$dataset2 = [];
$timestamp = current($options);
$timestamp2 = current($options);

if (!empty($_POST['timestamp1']) && !empty($_POST['timestamp2'])) {
    $timestamp = $_POST['timestamp1'];
    $timestamp2 = $_POST['timestamp2'];

    if (is_numeric($timestamp)) {
        $status_code_counts = $resultsStorage->getStatusCodeTotals($timestamp);
    }

    if (is_numeric($timestamp2)) {
        $status_code_counts2 = $resultsStorage->getStatusCodeTotals($timestamp2);
    }

    // Consolidate all status codes.
    $codes = array_unique(array_merge(array_keys($status_code_counts), array_keys($status_code_counts2)));

    // Map the data for each dataset, zero'ing missing entries.
    foreach ($codes as $code) {
        $dataset1[$code] = empty($status_code_counts[$code]) ? 0 : $status_code_counts[$code];
        $dataset2[$code] = empty($status_code_counts2[$code]) ? 0 : $status_code_counts2[$code];
    }
}

// Specify our Twig templates location
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../src/templates');
// Instantiate our Twig
$twig = new \Twig\Environment($loader);
$template = $twig->load('charts.twig');
echo $template->render(
    [
        'options' => $options,
        'codes' => '[' . implode(',', $codes) . ']',
        'dataset1' =>  '[' . implode(',', $dataset1) . ']',
        'dataset2' =>  '[' . implode(',', $dataset2) . ']',
        'select1' => $timestamp,
        'select2' => $timestamp2,
    ]
);
