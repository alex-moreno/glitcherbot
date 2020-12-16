<?php

namespace ScraperBot\Command;

use ScraperBot\Command\CrawlSitesCommand;
use ScraperBot\Source\SitesJsonSource;
use ScraperBot\Source\XmlSitemapSource;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Crawl a list of sites directly from a sitemap.xml file.
 *
 * @package Command\Acquia
 */
class CrawlXmlSitemapCommand extends CrawlSitesCommand {

    protected static $defaultName = 'bot:crawl-xml-sitemap';

    /**
     * @inheritDoc
     */
    protected function configure() {
        $this
            ->setDescription('Crawls sites from an xnl sitemap file.')
            ->addArgument('sitemap_file', InputArgument::REQUIRED, 'Path to the sitemap file.')
            ->addOption('config_file', null, InputArgument::OPTIONAL, 'Path to the config file', 'config.php')
            ->addOption('destination_folder', null, InputArgument::OPTIONAL, 'Path to the destination folder for results', '.')
            ->addOption('use_base_uri', null, InputOption::VALUE_NONE, 'If specified, ask guzzle to create a new client each time, in order to specify base URI for redirects.');
    }

    /**
     * @inheritDoc
     */
    protected function getSource(InputInterface $input) {
        $file = $input->getArgument('sitemap_file');
        return new XmlSitemapSource($file);
    }

}
