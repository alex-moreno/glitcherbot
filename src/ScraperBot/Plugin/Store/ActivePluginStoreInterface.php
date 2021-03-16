<?php


namespace ScraperBot\Plugin;


use ScraperBot\Plugin\Type\Plugin;
use ScraperBot\Plugin\Type\Plugin\Store;

/**
 * Interface ActivePluginStoreInterface
 * @package ScraperBot\Plugin
 */
interface ActivePluginStoreInterface {

    /**
     * Activate a plugin.
     *
     * @param Plugin $plugin
     * @return mixed
     */
    public function activatePlugin(Plugin $plugin);

    /**
     * De-activate a plugin.
     *
     * @param Plugin $plugin
     * @return mixed
     */
    public function deactivatePlugin(Plugin $plugin);

    /**
     * Check if a plugin is active.
     *
     * @param Plugin $plugin
     * @return bool
     */
    public function isActive(Plugin $plugin);

    /**
     * Get the list of active plugins.
     *
     * @return array
     */
    public function getActivePluginList();
}
