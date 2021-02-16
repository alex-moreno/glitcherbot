<?php

namespace ScraperBot\Core;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Class Bootstrap
 * @package ScraperBot\Core
 */
class Bootstrap {

    public function init() {
        $containerBuilder = new ContainerBuilder();
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        GlitcherBot::setContainer($containerBuilder);
    }

}
