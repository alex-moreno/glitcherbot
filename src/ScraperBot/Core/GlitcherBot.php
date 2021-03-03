<?php


namespace ScraperBot\Core;

use Composer\Autoload\ClassLoader;
use ScraperBot\Plugin\PluginRegistryInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class GlitcherBot {

    private static $container = NULL;
    private static $autoloader = NULL;

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

    public static function getPluginTypes() {
        $pluginRegistry = GlitcherBot::service('glitcherbot.plugin_registry');
        return $pluginRegistry->getPluginTypes();
    }

    public static function getPlugins() {
        /**
         * @type $pluginRegistry PluginRegistryInterface
         */
        $pluginRegistry = GlitcherBot::service('glitcherbot.plugin_registry');
        return $pluginRegistry->getPlugins();
    }

    public static function getActivePluginList() {
        $pluginRegistry = GlitcherBot::service('glitcherbot.active_plugin_store');
        return $pluginRegistry->getActivePluginList();
    }

    public static function addNamespace($namespace, $src_path) {
        self::getAutoloader()->addPsr4($namespace, [$src_path]);
    }

    /**
     * @return ClassLoader
     */
    public static function getAutoloader() {
        return self::$autoloader;
    }

    /**
     * @param null $autoloader
     */
    public static function setAutoloader($autoloader): void {
        self::$autoloader = $autoloader;
    }

}
