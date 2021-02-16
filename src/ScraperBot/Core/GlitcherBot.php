<?php


namespace ScraperBot\Core;

class GlitcherBot {

    private static $container = NULL;

    /**
     * @param null $container
     */
    public static function setContainer($container): void {
        self::$container = $container;
    }

    public static function service($name) {
        return self::$container->get($name);
    }

}
