<?php


namespace ScraperBot\Subscriber;


use ScraperBot\Core\GlitcherBot;
use ScraperBot\Plugin\Event\PluginDiscoveryEvent;
use ScraperBot\Plugin\Type\Plugin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class YamlPluginDiscoverySubscriber implements EventSubscriberInterface {

    private $base_folder = __DIR__ . '/../../';
    private $custom_folder = __DIR__ . '/../../custom';

    private $pattern = '%type.plugins.yaml';

    public function onPluginDiscovery(PluginDiscoveryEvent $event) {
        $this->scanForPlugins($this->base_folder, $event);
        $this->scanForPlugins($this->custom_folder, $event, TRUE);
    }

    private function scanForPlugins($folder, PluginDiscoveryEvent $event, $addNamespace = FALSE) {
        // Search folders to discover *.plugin.type.yml files
        $finder = new Finder();

        if (!file_exists($folder)) {
            return;
        }

        $pattern = str_replace('%type', $event->getType(), $this->pattern);

        // Add data to instances of PluginType class

        /**
         * @type $file \SplFileInfo
         */
        foreach ($finder->files()->in($folder)->name($pattern) as $file) {
            $definitions = Yaml::parse($file->getContents());

            foreach ($definitions as $key => $meta) {
                $event->addPlugin(new Plugin($key, $meta['description'], $meta['class']));
            }

            $file_folder = $file->getPath();
            $src_path = $file_folder . DIRECTORY_SEPARATOR . "src";

            $parent_folder = basename($file_folder);

            $namespace = "ScraperBot\\" . $parent_folder . "\\";

            if (file_exists($src_path)) {
                // add namspace to autoloader
                GlitcherBot::addNamespace($namespace, $src_path);
            }
        }
    }

    public static function getSubscribedEvents() {
        return [
            PluginDiscoveryEvent::NAME => 'onPluginDiscovery',
        ];
    }

}