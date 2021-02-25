<?php

namespace ScraperBot\Core\Http;

use ScraperBot\Core\Bootstrap;
use ScraperBot\Core\GlitcherBot;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

/**
 * GlitcherBot HTTP Kernel.
 *
 * @package ScraperBot\Core\Http
 */
class GlitcherBotKernel extends HttpKernel {

    /**
     * GlitcherBotKernel constructor.
     */
    public function __construct() {
        Bootstrap::init();

        $dispatcher = GlitcherBot::service('glitcherbot.event_dispatcher');
        $resolver = GlitcherBot::service('glitcherbot.controller_resolver');
        $argumentResolver = GlitcherBot::service('glitcherbot.argument_resolver');
        $route_manager = GlitcherBot::service('glitcherbot.route_manager');

        $route_collection = $route_manager->getRoutes();

        $matcher = new UrlMatcher($route_collection, new RequestContext());
        $dispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));

        parent::__construct($dispatcher, $resolver, new RequestStack(), $argumentResolver);
    }

}
