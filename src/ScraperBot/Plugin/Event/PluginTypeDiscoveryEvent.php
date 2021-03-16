<?php


namespace ScraperBot\Plugin\Event;


use ScraperBot\Plugin\Type\PluginType;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event that is fired during plugin type discovery.
 *
 * @package ScraperBot\Plugin\Event
 */
class PluginTypeDiscoveryEvent extends Event {

    public const NAME = 'glitcherbot.plugin_type_discovery';

    private $plugin_meta = [];

    /**
     * Register a plugin with the system.
     *
     * @param $pluginType
     */
    public function addPlugin(PluginType $pluginType) {
        $this->plugin_meta[] = $pluginType;
    }

    /**
     * @return array
     */
    public function getPluginTypes(): array {
        return $this->plugin_meta;
    }

}
