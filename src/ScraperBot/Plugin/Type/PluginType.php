<?php


namespace ScraperBot\Plugin\Type;

/**
 *
 * @package ScraperBot\Plugin\Meta
 */
class PluginType {

    private $interface = NULL;
    private $type = NULL;
    private $human_name = NULL;

    /**
     * PluginType constructor.
     * @param $type
     *  The plugin type.
     * @param $human_name
     *  The human readable name of the plugin.
     * @param $interface
     *  The fully qualified interface name that plugins of this type should implement.
     */
    public function __construct($type, $human_name, $interface) {
        $this->type = $type;
        $this->human_name = $human_name;
        $this->interface = $interface;
    }

    /**
     * @return string
     *  The interface that should be implemented by plugins of this type.
     */
    public function getInterface() {
        return $this->interface;
    }

    /**
     * @param null $interface
     */
    public function setInterface($interface): void {
        $this->interface = $interface;
    }

    /**
     * @return null
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param null $type
     */
    public function setType($type): void {
        $this->type = $type;
    }

    /**
     * @return null
     */
    public function getHumanName() {
        return $this->human_name;
    }

    /**
     * @param null $human_name
     */
    public function setHumanName($human_name): void {
        $this->human_name = $human_name;
    }

}
