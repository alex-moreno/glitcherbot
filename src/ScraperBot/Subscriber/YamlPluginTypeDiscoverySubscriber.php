<?php


namespace ScraperBot\Subscriber;


use ScraperBot\Plugin\Event\PluginTypeDiscoveryEvent;
use ScraperBot\Plugin\Type\PluginType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class YamlPluginTypeDiscoverySubscriber implements EventSubscriberInterface {

    private $base_folder = __DIR__ . '/../../';
    private $custom_folder = __DIR__ . '/../../../custom';

    private $pattern = '*.plugin.type.yaml';

    public function onPluginTypeDiscovery(PluginTypeDiscoveryEvent $event) {
        $this->scanForPlugins($this->base_folder, $event);
        $this->scanForPlugins($this->custom_folder, $event);
    }

    private function scanForPlugins($folder, PluginTypeDiscoveryEvent $event) {
        // Search folders to discover *.plugin.type.yml files
        $finder = new Finder();

        if (!file_exists($folder)) {
            return;
        }

        // Add data to instances of PluginType class
        foreach ($finder->files()->in($folder)->name($this->pattern) as $file) {
            $definition = Yaml::parse($file->getContents());

            $type = new PluginType($definition['type'], $definition['name'], $definition['interface']);
            $event->addPlugin($type);
        }
    }

    public static function getSubscribedEvents() {
        return [
            PluginTypeDiscoveryEvent::NAME => 'onPluginTypeDiscovery',
        ];
    }

}
