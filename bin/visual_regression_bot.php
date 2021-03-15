#!/usr/bin/env php
<?php

use ScraperBot\Core\Bootstrap;
use ScraperBot\Core\GlitcherBot;
use Symfony\Component\Console\Application;

$autoloader = require __DIR__.'/../vendor/autoload.php';
GlitcherBot::setAutoloader($autoloader);

Bootstrap::init();

$application = new Application("Visual Regression Bot");
$application->add(new \ScraperBot\Command\CrawlSitesCommand());
$application->add(new \ScraperBot\Command\CrawlXmlSitemapCommand());
$application->add(new \ScraperBot\Command\CompareCrawlsCommand());
$application->add(new \ScraperBot\Command\Acquia\GenerateSiteListCommand());
$application->add(new \ScraperBot\Command\Acquia\AcsfCrawlSitesCommand());

$application->run();
