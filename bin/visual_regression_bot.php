#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application("Visual Regression Bot");
$application->add(new \Command\CrawlSitesCommand());
$application->add(new \Command\Acquia\GenerateSiteListCommand());
$application->run();
