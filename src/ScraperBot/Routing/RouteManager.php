<?php

namespace ScraperBot\Routing;

use Symfony\Component\Routing\Route;

/**
 * Class RouteManager
 * @package ScraperBot\Routing
 */
class RouteManager {

    /**
     * Return a list of routes for the application.
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRoutes() {
        $routes = new \Symfony\Component\Routing\RouteCollection();

        $route = new Route('/', array('_controller' => [\ScraperBot\Routing\Controllers\IndexController::class, 'handle']));
        $routes->add('index', $route);

        $route = new Route('/sites', array('_controller' => [\ScraperBot\Routing\Controllers\SitesController::class, 'handle']));
        $routes->add('sites', $route);

        $route = new Route('/charts', array('_controller' => [\ScraperBot\Routing\Controllers\ChartsController::class, 'handle']));
        $routes->add('charts', $route);

        $route = new Route('/stats/site', array('_controller' => [\ScraperBot\Routing\Controllers\SiteStatsController::class, 'handle']));
        $routes->add('site-stats', $route);

        $route = new Route('/compare', array('_controller' => [\ScraperBot\Routing\Controllers\ComparisonController::class, 'handle']));
        $routes->add('compare', $route);

        $route = new Route('/stats/error-codes', array('_controller' => [\ScraperBot\Routing\Controllers\ErrorCodesController::class, 'handle']));
        $routes->add('error-codes', $route);

        $route = new Route('/stats/result-changes', array('_controller' => [\ScraperBot\Routing\Controllers\ResultChangedController::class, 'handle']));
        $routes->add('result-changes', $route);

        return $routes;
    }

}