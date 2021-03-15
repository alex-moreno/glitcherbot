<?php


namespace ScraperBot\Plugin\Store;


use ScraperBot\Core\GlitcherBot;
use ScraperBot\Plugin\ActivePluginStoreInterface;
use ScraperBot\Plugin\PluginRegistry;
use ScraperBot\Plugin\PluginRegistryInterface;
use ScraperBot\Plugin\Type\Plugin;
use Symfony\Component\Yaml\Yaml;

/**
 * Stores active plugin list as YAML file.
 *
 * @package ScraperBot\Plugin\Store
 */
class YamlActivePluginStore implements ActivePluginStoreInterface {

    private $filepath = NULL;
    private $plugins = NULL;
    private $activeList = NULL;

    public function __construct($filepath = NULL) {
        if (empty($filepath)) {
            $filepath = __DIR__ . "/../../../../active_plugin_store.yaml";
        }

        $this->filepath = $filepath;
    }

    public function activatePlugin(Plugin $plugin) {
    }

    public function deactivatePlugin(Plugin $plugin) {
    }

    public function isActive(Plugin $plugin) {
    }

    /**
     * @return mixed|null
     */
    public function getActivePluginList() {
        if (empty($this->activeList)) {
            $this->loadActivePlugins();
        }

        return $this->activeList;
    }

    /**
     * @return array
     */
    public function getActivePlugins($type = NULL): array {
        if ($this->plugins == NULL) {
            $this->loadActivePlugins();
        }

        if ($type == NULL) {
            return $this->plugins;
        }

        return isset($this->plugins[$type]) ? $this->plugins[$type] : [];
    }

    /**
     * @return array|null
     */
    private function loadActivePlugins() {
        if (!file_exists($this->filepath)) {
            // TODO throw a warning.
            // Default to SQLite3 storage plugin.
            $this->activeList = ['storage' => ['sqlite3']];
        }

        if (empty($this->activeList)) {
            $this->activeList = Yaml::parseFile($this->filepath);
        }

        /**
         * @type $registry PluginRegistryInterface
         */
        $registry = GlitcherBot::service('glitcherbot.plugin_registry');

        foreach ($this->activeList as $type => $implementations) {
            foreach ($implementations as $name) {
                $plugin = $registry->getPlugin($type, $name);

                if (!empty($plugin)) {
                    $this->plugins[$type][$name] = $plugin;
                }
            }
        }

        return $this->plugins;
    }
}
