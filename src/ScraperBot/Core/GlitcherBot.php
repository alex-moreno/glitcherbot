<?php


namespace ScraperBot\Core;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class GlitcherBot {

    private static $container = NULL;

    /**
     * @return null
     */
    public static function getContainer() {
        if (self::$container == NULL) {
            self::$container = new ContainerBuilder();
            $loader = new YamlFileLoader(self::$container, new FileLocator(__DIR__ . '/../../config'));
            $loader->load('services.yaml');
        }

        return self::$container;
    }

    /**
     * @param $name
     * @return mixed
     */
    public static function service($name) {
        return self::getContainer()->get($name);
    }

}
