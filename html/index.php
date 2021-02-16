<?php

use ScraperBot\Core\Bootstrap;
use ScraperBot\Core\Http\GlitcherBotKernel;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/../vendor/autoload.php';

$request = Request::createFromGlobals();
$bootstrap = new Bootstrap();
$bootstrap->init();

$kernel = new GlitcherBotKernel();

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
