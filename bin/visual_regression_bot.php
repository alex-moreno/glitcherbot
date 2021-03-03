#!/usr/bin/env php
<?php

$autoloader = require __DIR__.'/../vendor/autoload.php';
GlitcherBot::setAutoloader($autoloader);

use ScraperBot\Core\Bootstrap;
use Symfony\Component\Console\Application;

Bootstrap::init();

$application = new Application("Visual Regression Bot");
$application->add(new \ScraperBot\Command\CrawlSitesCommand());
$application->add(new \ScraperBot\Command\CrawlXmlSitemapCommand());
$application->add(new \ScraperBot\Command\CompareCrawlsCommand());
$application->add(new \ScraperBot\Command\Acquia\GenerateSiteListCommand());
$application->add(new \ScraperBot\Command\Acquia\AcsfCrawlSitesCommand());

$application->run();
