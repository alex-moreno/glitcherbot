<?php
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

require_once __DIR__.'/../vendor/autoload.php';

$request = Request::createFromGlobals();
$dispatcher = new EventDispatcher();
$resolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

$route_manager = new \ScraperBot\Routing\RouteManager();
$route_collection = $route_manager->getRoutes();

$matcher = new UrlMatcher($route_collection, new RequestContext());
$dispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));

$kernel = new \Symfony\Component\HttpKernel\HttpKernel(
    $dispatcher,
    $resolver,
    new RequestStack(),
    $argumentResolver
);

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
