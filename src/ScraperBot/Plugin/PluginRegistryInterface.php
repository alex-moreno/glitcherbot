<?php


namespace ScraperBot\Plugin;


interface PluginRegistryInterface {

    /**
     * Get all plugins of the specified type.
     *
     * @return mixed
     */
    public function getPluginTypes();

    public function getPlugin($type, $id);
}
