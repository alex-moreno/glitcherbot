<?php

namespace ScraperBot\Routing\Controllers;

use ScraperBot\Core\GlitcherBot;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SiteStatsController
 * @package ScraperBot\Routing\Controllers
 */
class SiteStatsController {

    public function handle(Request $request) {
        $resultsStorage = GlitcherBot::service('glitcherbot.storage');

        $compare = [];
        if (isset($_GET['url'])) {
            $url = $_GET['url'];
        }

        if (isset($_GET['date1']) && $_GET['date2']) {
            $compare['date1'] = $_GET['date1'];
            $compare['date2'] = $_GET['date2'];
        }

        $resultsStorage->getTimeStamps($compare);

        if (isset($_GET['latest'])) {
            $onlyLatest = $_GET['latest'];
        }
        $crawls = $resultsStorage->getTimeStamps($compare, $onlyLatest);

        $rows = [];
        $headers = [];
        $index = 0;

        $tolerance = 1000;
        if (isset($_GET['tolerance'])) {
            $tolerance = $_GET['tolerance'];
        }

        // Iterate over the results, preparing columns and rows for the twig template.
        foreach ($crawls as $timestamp) {
            // Prepare the headers for the table.
            $headers[$index] = $timestamp;

            // Get site crawl results for each timestamp.
            $resultsByTimestamp = $resultsStorage->getResultsbyTimestamp($timestamp, $onlyLatest, $url);

            if (!empty($resultsByTimestamp)) {
                // Get the list of results, per site, for a given timestamp and prepare
                // array entries representing the rows.
                foreach ($resultsByTimestamp as $listOfSites) {
                    foreach ($listOfSites as $site) {
                        $site_id = $site['url'];

                        // Initialise the row for the site if it's empty.
                        if (empty($rows[$site_id][$index])) {
                            $rows[$site_id][$index] = [];
                        }
                        array_push($rows[$site_id][$index], $site['size'], $site['statusCode'], $site['naughty'], $site['url'], $site['tags']);
                    }
                }
            }

            $index++;
        }

        $data = ['headers' => $headers, 'rows' => $rows, 'tolerance' => $tolerance, 'date1' => $compare['date1'], 'date2' => $compare['date2']];

        $response = new \Symfony\Component\HttpFoundation\Response();
        $renderer = GlitcherBot::service('glitcherbot.renderer');
        $content = $renderer->render('results_site_stats.twig', $data);
        $response->setContent($content);

        return $response;
    }

}
