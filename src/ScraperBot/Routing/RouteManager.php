<?php


namespace ScraperBot\Routing;

class RouteManager {

    public function getRoutes() {
        $routes = new \Symfony\Component\Routing\RouteCollection();

        $route = new \Symfony\Component\Routing\Route('/', array('_controller' => [\ScraperBot\Routing\Controllers\IndexController::class, 'handle']));
        $routes->add('index', $route);

        return $routes;
    }

}