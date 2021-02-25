<?php

namespace ScraperBot\Renderer;

class TwigRenderer {

    /**
     * Render the given template with the supplied data.
     *
     * @param $template
     * @param array $data
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render($template, $data = []) {
        // Specify our Twig templates location
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../templates');
        // Instantiate our Twig
        $twig = new \Twig\Environment($loader, [
        'debug' => false,]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());

        $template = $twig->load($template);

        return $template->render($data);
    }
}