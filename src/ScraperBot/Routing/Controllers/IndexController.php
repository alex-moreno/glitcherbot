<?php

namespace ScraperBot\Routing\Controllers;

use ScraperBot\Renderer\TwigRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class IndexController
 * @package ScraperBot\Routing\Controllers
 */
class IndexController {

    /**
     * Handle a request for the index.
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request) {
        $response = new Response();
        $renderer = new TwigRenderer();
        $content = $renderer->render('index.twig');
        $response->setContent($content);

        return $response;
    }

}
