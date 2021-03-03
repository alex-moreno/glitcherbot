<?php


namespace ScraperBot\Plugin;


interface PluginRegistryInterface {

    /**
     * Get all plugin types.
     *
     * @return mixed
     */
    public function getPluginTypes();

    /**
     * Get a specific plugin by type and ID.
     *
     * Type is required for scope, since identifiers are only unique for a given plugin type.
     *
     * @param $type
     * @param $id
     * @return mixed
     */
    public function getPlugin($type, $id);

    /**
     * @return mixed
     */
    public function getPlugins();
}
