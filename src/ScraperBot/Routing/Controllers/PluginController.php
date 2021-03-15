<?php


namespace ScraperBot\Routing\Controllers;


use ScraperBot\Core\GlitcherBot;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PluginController {

    public function handle(Request $request) {
        $response = new Response();
        $renderer = GlitcherBot::service('glitcherbot.renderer');

        $plugin_types = GlitcherBot::getPluginTypes();

        foreach ($plugin_types as $plugin_type) {
            $types[$plugin_type->getType()]['name'] = $plugin_type->getHumanName();
            $types[$plugin_type->getType()]['interface'] = $plugin_type->getInterface();
            $types[$plugin_type->getType()]['singleton'] = $plugin_type->isSingleton();
        }

        $plugin_definitions = GlitcherBot::getPlugins();
        $active_list = GlitcherBot::getActivePluginList();

        $warnings = [];

        foreach ($plugin_definitions as $type => $plugins) {

            if ($types[$type]['singleton'] && count($plugins) > 1) {
                $warnings[$type]['message'] = "You have more than one active plugin of type '" . $type . "' but it is intended to be used as a singleton.";
            }

            foreach ($plugins as $id => $plugin) {
                $plugin_data[$type][$id]['id'] = $id;
                $plugin_data[$type][$id]['name'] = $plugin->getDescription();
                $plugin_data[$type][$id]['class'] = $plugin->getClass();
                $plugin_data[$type][$id]['active'] = in_array($id, $active_list[$type]) ? "YES" : "NO";
            }
        }

        $content = $renderer->render('plugins.twig', ['plugin_types' => $types, 'plugins' => $plugin_data, 'warnings' => $warnings]);

        $response->setContent($content);

        return $response;
    }

}
