<?php


namespace ScraperBot\Plugin;

use ScraperBot\Plugin\Event\PluginDiscoveryEvent;
use ScraperBot\Plugin\Event\PluginTypeDiscoveryEvent;
use ScraperBot\Plugin\Type\Plugin;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PluginRegistry implements PluginRegistryInterface {

    private $dispatcher = NULL;
    private $pluginTypes = [];
    private $implementations = [];

    public function __construct(EventDispatcher $dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    public function getPluginTypes() {
        if (empty($this->pluginTypes)) {
            // Discover plugin list.
            $event = new PluginTypeDiscoveryEvent();
            $this->dispatcher->dispatch($event, PluginTypeDiscoveryEvent::NAME);

            $this->pluginTypes = $event->getPluginTypes();
        }

        return $this->pluginTypes;
    }

    public function getPlugins() {
        $types = $this->getPluginTypes();

        foreach ($types as $type) {
            $this->getImplementations($type->getType());
        }

        return $this->implementations;
    }

    private function getImplementations($type) {
        if (empty($this->implementations)) {
            $event = new PluginDiscoveryEvent($type);
            $this->dispatcher->dispatch($event, PluginDiscoveryEvent::NAME);
            $this->implementations[$event->getType()] = $event->getPlugins();
        }

        return $this->implementations;
    }

    public function getPlugin($type, $id) {
        $this->getPlugins();

        if (isset($this->implementations[$type])) {
            foreach ($this->implementations[$type] as $plugin) {
                /**
                 * @type $plugin Plugin
                 */
                if ($plugin->getId() == $id) {
                    $plugin_class = $plugin->getClass();
                    return new $plugin_class();
                }
            }
        }

        return NULL;
    }

}
