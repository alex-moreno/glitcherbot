<?php


namespace ScraperBot\Plugin\Event;


use ScraperBot\Plugin\Type\Plugin;
use Symfony\Contracts\EventDispatcher\Event;

class PluginDiscoveryEvent extends Event {

    public const NAME = 'glitcherbot.plugin_discovery';
    private $type = NULL;

    private $plugins = [];

    public function __construct($type) {
        $this->type = $type;
    }

    public function addPlugin(Plugin $plugin) {
        $this->plugins[$plugin->getId()] = $plugin;
    }

    public function getPlugins() {
        return $this->plugins;
    }

    /**
     * @return null
     */
    public function getType() {
        return $this->type;
    }

}