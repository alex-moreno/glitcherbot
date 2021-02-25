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

    private static $initialised = FALSE;

    /**
     * Perform initial bootstrap.
     */
    public static function init() {
        if (!self::$initialised) {
            // Ensure the container is initialised by simply asking for it.
            GlitcherBot::getContainer();
            self::$initialised = TRUE;
        }
    }

}
