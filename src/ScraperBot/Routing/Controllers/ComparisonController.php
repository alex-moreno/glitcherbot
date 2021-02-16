<?php

namespace ScraperBot\Routing\Controllers;

use ScraperBot\Core\GlitcherBot;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ComparisonController
 * @package ScraperBot\Routing\Controllers
 */
class ComparisonController {

    public function handle(Request $request) {
        $resultsStorage = GlitcherBot::service('glitcherbot.storage');
        $crawls = $resultsStorage->getTimeStamps();

        $rows = [];
        $headers = [0,1];
        $index = 0;
        $tolerance = 1000;

        if (isset($_GET['tolerance'])) {
            $tolerance = $_GET['tolerance'];
        }

        $lastElem = array_key_last($crawls);
        $rows = $resultsStorage->getCrawlDiffs($crawls[$lastElem-1], $crawls[$lastElem], $tolerance);
        $data = ['headers' => $headers, 'rows' => $rows, 'tolerance' => $tolerance];

        $response = new Response();
        $renderer = GlitcherBot::service('glitcherbot.renderer');
        $content = $renderer->render('crawls_diffs.twig', $data);
        $response->setContent($content);

        return $response;
    }

}

