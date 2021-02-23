<?php

namespace ScraperBot\Command\Acquia;

use ScraperBot\Command\CrawlSitesCommand;
use ScraperBot\Source\SitesJsonSource;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Crawl a list of sites directly from a sites.json file.
 *
 * @package Command\Acquia
 */
class AcsfCrawlSitesCommand extends CrawlSitesCommand {

    protected static $defaultName = 'acquia:acsf-crawl-sites';

    /**
     * @inheritDoc
     */
    protected function configure() {
        $this
            ->setDescription('Crawls sites from an Acquia Cloud Site Factory sites.json file.')
            ->addArgument('sites_json_file', InputArgument::REQUIRED, 'Path to the sites.json file.')
            ->addOption('config_file', null, InputArgument::OPTIONAL, 'Path to the config file', 'config.php')
            ->addOption('destination_folder', null, InputArgument::OPTIONAL, 'Path to the destination folder for results', '.')
            ->addOption('use_base_uri', null, InputOption::VALUE_NONE, 'If specified, ask guzzle to create a new client each time, in order to specify base URI for redirects.')
            ->addOption('include_sitemaps', null, InputArgument::OPTIONAL, 'Crawl urls found in sitemaps', FALSE);
    }

    /**
     * @inheritDoc
     */
    protected function getSource(InputInterface $input) {
        $file = $input->getArgument('sites_json_file');
        return new SitesJsonSource($file);
    }

}
