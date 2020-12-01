<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new \Command\CrawlSitesCommand());
$application->add(new \Command\Acquia\GenerateSiteListCommand());
$application->run();
