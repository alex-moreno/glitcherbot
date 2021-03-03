<?php

use ScraperBot\Core\GlitcherBot;
use ScraperBot\Core\Http\GlitcherBotKernel;
use Symfony\Component\HttpFoundation\Request;

/**
 * @type $autoloader \Composer\Autoload\ClassLoader
 */
$autoloader = require_once __DIR__.'/../vendor/autoload.php';
GlitcherBot::setAutoloader($autoloader);

$request = Request::createFromGlobals();

$kernel = new GlitcherBotKernel();

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
