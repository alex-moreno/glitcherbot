<?php


namespace ScraperBot\Plugin\Event;


use Symfony\Contracts\EventDispatcher\Event;

class PluginTypeDiscoveryEvent extends Event {

    public const NAME = 'glitcherbot.plugin_type_discovery';

    private $plugin_meta = [];

    public function addPlugin($metadata) {
        $this->plugin_meta[] = $metadata;
    }

    /**
     * @return array
     */
    public function getPluginTypes(): array {
        return $this->plugin_meta;
    }

}
