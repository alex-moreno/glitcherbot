<?php

namespace ScraperBot\Core;

use ScraperBot\Plugin\Type\Plugin;
use ScraperBot\Subscriber\YamlPluginDiscoverySubscriber;
use ScraperBot\Subscriber\YamlPluginTypeDiscoverySubscriber;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

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

            self::discoverSubscribers($dispatcher);
        }
    }

    private static function discoverSubscribers(EventDispatcher $dispatcher) {
        // Search folders to discover subscriber YAML files.
        $finder = new Finder();

        $folder = __DIR__ . '/../../custom';

        if (!file_exists($folder)) {
            return;
        }

        $pattern = "*.subscribers.yaml";

        /**
         * @type $file \SplFileInfo
         */
        foreach ($finder->files()->in($folder)->name($pattern) as $file) {
            $definitions = Yaml::parse($file->getContents());

            $file_folder = $file->getPath();
            $src_path = $file_folder . DIRECTORY_SEPARATOR . "src";

            $parent_folder = basename($file_folder);

            $namespace = "ScraperBot\\" . $parent_folder . "\\";

            if (file_exists($src_path)) {
                // Add namespace to autoloader.
                GlitcherBot::addNamespace($namespace, $src_path);
            }

            // Instantiate the subscriber class and add it to dispatcher.
            foreach ($definitions as $key => $class) {
                if (class_exists($class)) {
                    $dispatcher->addSubscriber(new $class());
                }
                else {
                    // @todo error reporting/warning.
                }
            }
        }
    }

}
