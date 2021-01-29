<?php

namespace ScraperBot\Routing\Controllers;

use ScraperBot\Renderer\TwigRenderer;
use ScraperBot\Storage\SqlLite3Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ComparisonController
 * @package ScraperBot\Routing\Controllers
 */
class ComparisonController {

    public function handle(\Symfony\Component\HttpFoundation\Request $request) {
        $resultsStorage = new SqlLite3Storage('../glitcherbot.sqlite3');
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
        $renderer = new TwigRenderer();
        $content = $renderer->render('crawls_diffs.twig', $data);
        $response->setContent($content);

        return $response;
    }

}

