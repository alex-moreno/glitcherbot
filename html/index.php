<?php

require_once __DIR__.'/../vendor/autoload.php';

// Specify our Twig templates location
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../src/templates');
// Instantiate our Twig
$twig = new \Twig\Environment($loader);
$template = $twig->load('index.twig');
echo $template->render();
