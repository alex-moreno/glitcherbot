<?php

namespace ScraperBot\Core;

use ScraperBot\Subscriber\YamlPluginDiscoverySubscriber;
use ScraperBot\Subscriber\YamlPluginTypeDiscoverySubscriber;
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

            $dispatcher = GlitcherBot::service('glitcherbot.event_dispatcher');
            $dispatcher->addSubscriber(new YamlPluginTypeDiscoverySubscriber());
            $dispatcher->addSubscriber(new YamlPluginDiscoverySubscriber());
        }
    }

}
