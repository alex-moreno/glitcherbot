#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$bootstrap = new \ScraperBot\Core\Bootstrap();
$bootstrap->init();

$application = new Application("Visual Regression Bot");
$application->add(new \ScraperBot\Command\CrawlSitesCommand());
$application->add(new \ScraperBot\Command\CrawlXmlSitemapCommand());
$application->add(new \ScraperBot\Command\CompareCrawlsCommand());
$application->add(new \ScraperBot\Command\Acquia\GenerateSiteListCommand());
$application->add(new \ScraperBot\Command\Acquia\AcsfCrawlSitesCommand());

$application->run();
