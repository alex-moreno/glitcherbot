<?php

namespace ScraperBot\Core\Http;

use ScraperBot\Core\GlitcherBot;
use ScraperBot\Routing\RouteManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class GlitcherBotKernel extends HttpKernel {

    /**
     * GlitcherBotKernel constructor.
     */
    public function __construct() {
        $dispatcher = GlitcherBot::service('glitcherbot.event_dispatcher');

        $resolver = new ControllerResolver();
        $argumentResolver = new ArgumentResolver();

        $route_manager = new RouteManager();
        $route_collection = $route_manager->getRoutes();

        $matcher = new UrlMatcher($route_collection, new RequestContext());
        $dispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));

        parent::__construct($dispatcher, $resolver, new RequestStack(), $argumentResolver);
    }

}
