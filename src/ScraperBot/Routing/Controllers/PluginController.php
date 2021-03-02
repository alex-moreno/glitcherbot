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
        }

        $plugin_definitions = GlitcherBot::getPlugins();
        $active_list = GlitcherBot::getActivePluginList();

        foreach ($plugin_definitions as $type => $plugins) {
            foreach ($plugins as $id => $plugin) {
                $plugin_data[$type]['id'] = $id;
                $plugin_data[$type]['name'] = $plugin->getDescription();
                $plugin_data[$type]['interface'] = $plugin->getClass();
                $plugin_data[$type]['active'] = in_array($id, $active_list[$type]) ? "YES" : "NO";
            }
        }

        $content = $renderer->render('plugins.twig', ['plugin_types' => $types, 'plugins' => $plugin_data]);

        $response->setContent($content);

        return $response;
    }

}
