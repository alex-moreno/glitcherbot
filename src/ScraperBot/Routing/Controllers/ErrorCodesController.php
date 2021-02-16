<?php

namespace ScraperBot\Routing\Controllers;

use ScraperBot\Core\GlitcherBot;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ErrorCodesController
 */
class ErrorCodesController {

    public function handle(Request $request) {
        $rows = [];
        $headers = [];
        $index = 0;

        $resultsStorage = GlitcherBot::service('glitcherbot.storage');
        $crawls = $resultsStorage->getTimeStamps();

        $statusCodes = $resultsStorage->getStatusCodes();
        // Add manually the total of sites
        array_push($statusCodes, 'Total');

        foreach ($statusCodes as $code) {
            $headers[$index] = $code;
            $statusResults = $resultsStorage->getStatsByStatus($code);
            foreach ($statusResults as $timestamp => $statusResult) {
                $index++;
                if ($code !== 'Total') {
                    $rows[$timestamp][$code] = $statusResult;
                }
                else {
                    $rows[$timestamp][$code] = $resultsStorage->getNumberofSites($timestamp);
                }
            }
        }

        $data = ['headers' => $headers, 'rows' => $rows, 'tolerance' => $tolerance];

        $response = new \Symfony\Component\HttpFoundation\Response();
        $renderer = GlitcherBot::service('glitcherbot.renderer');
        $content = $renderer->render('results_status.twig', $data);
        $response->setContent($content);

        return $response;
    }

}
