<?php


namespace ScraperBot\Plugin;

use ScraperBot\Plugin\Event\PluginDiscoveryEvent;
use ScraperBot\Plugin\Event\PluginTypeDiscoveryEvent;
use ScraperBot\Plugin\Type\Plugin;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PluginRegistry implements PluginRegistryInterface {

    private $dispatcher = NULL;
    private $plugin_types = [];
    private $plugin_instances = [];

    public function __construct(EventDispatcher $dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Return a list of known plugin types.
     *
     * If the existing list is empty, the method will attempt
     * to discover types.
     *
     * @return array|mixed
     */
    public function getPluginTypes() {
        if (empty($this->plugin_types)) {
            // Discover plugin list.
            $event = new PluginTypeDiscoveryEvent();
            $this->dispatcher->dispatch($event, PluginTypeDiscoveryEvent::NAME);

            $this->plugin_types = $event->getPluginTypes();
        }

        return $this->plugin_types;
    }

    /**
     * Discover plugin instances for all known types.
     *
     * @inheritDoc
     */
    public function getPlugins() {
        // Get the list of registered plugin types.
        $types = $this->getPluginTypes();

        // Discover plugin instances for each type.
        foreach ($types as $type) {
            $this->discoverInstances($type->getType());
        }

        return $this->plugin_instances;
    }

    /**
     * Discover plugin instances.
     *
     * @param $type
     */
    private function discoverInstances($type) {
        if (empty($this->plugin_instances)) {
            $event = new PluginDiscoveryEvent($type);
            $this->dispatcher->dispatch($event, PluginDiscoveryEvent::NAME);
            $this->plugin_instances[$event->getType()] = $event->getPlugins();
        }
    }

    /**
     * Get an instance of a plugin.
     *
     * This method will instantiate the requested plugin, if it exists.
     *
     * @param $type
     * @param $id
     * @return mixed|null
     */
    public function getPlugin($type, $id) {
        static $map = [];

        // Return an instance from static cache, if it exists.
        if (isset($map[$type][$id])) {
            return $map[$type][$id];
        }

        $this->getPlugins();

        if (isset($this->plugin_instances[$type])) {
            foreach ($this->plugin_instances[$type] as $plugin) {
                /**
                 * @type $plugin Plugin
                 */
                if ($plugin->getId() == $id) {
                    $plugin_class = $plugin->getClass();
                    $instance = new $plugin_class();

                    $map[$type][$id] = $instance;
                    return $map[$type][$id];
                }
            }
        }

        return NULL;
    }

}
