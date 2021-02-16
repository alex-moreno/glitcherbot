<?php
declare(strict_types=1);

namespace ScraperBot\Command;

use GuzzleHttp\Client;
use ScraperBot\Command\Subscriber\CrawlCsvLoggerSubscriber;
use ScraperBot\Command\Subscriber\CrawlSubscriber;
use ScraperBot\Core\GlitcherBot;
use ScraperBot\Source\CsvSource;
use ScraperBot\Source\SitesArraySource;
use ScraperBot\Source\XmlSitemapSource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command to crawl a supplied list of sites.
 *
 * @package Command
 */
class CrawlSitesCommand extends GlitcherBotCommand {

    protected static $defaultName = 'bot:crawl-sites';

    private $use_base_uri = FALSE;

    private $input = NULL;
    private $output = NULL;

    private $crawler = NULL;

    private $default_config = NULL;
    private $default_client = NULL;

    /**
     * @inheritDoc
     */
    protected function configure() {
        $this
            ->setDescription('Crawls a supplied list of sites to scrape data.')
            ->addArgument('sites_csv_file', InputArgument::REQUIRED, 'Path to the CSV file containing URLs.')
            ->addOption('config_file', null, InputArgument::OPTIONAL, 'Path to the config file', 'config.php')
            ->addOption('destination_folder', null, InputArgument::OPTIONAL, 'Path to the destination folder for results', '.')
            ->addOption('use_base_uri', null, InputOption::VALUE_NONE, 'If specified, ask guzzle to create a new client each time, in order to specify base URI for redirects.');
    }

    private function init($config_file) {
        $crawl_subscriber = new CrawlSubscriber($this->output);
        $this->eventDispatcher->addSubscriber($crawl_subscriber);

        $this->use_base_uri = $this->input->getOption('use_base_uri');

        $this->default_config = ['defaults' => [
            'verify' => false
        ]];

        // HTTP Client.
        $this->default_client = new Client($this->default_config);

        // Unless configured, do not ask the crawler to use a base URI.
        if (empty($this->use_base_uri)) {
            $this->default_config = NULL;
        }

        if (!file_exists($config_file)) {
            $this->output->writeln("<error>Could not locate config file: " . $config_file .  "</error>");
            return Command::FAILURE;
        }

        $this->output->writeln("Using config file: " . $config_file, OutputInterface::VERBOSITY_VERBOSE);
        $config = include('config.php');
        $this->output->writeln("Using config: " . print_r($config, TRUE), OutputInterface::VERBOSITY_DEBUG);

        $this->crawler = GlitcherBot::service('glitcherbot.crawler');
        $this->crawler->setHttpConfig($config);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->input = $input;
        $this->output = $output;

        $config_file = $this->input->getOption('config_file');

        $this->init($config_file);

        // @todo fire 'crawl command started' event.
        $output->writeln('Starting crawling. Date: ' . date('l jS \of F Y h:i:s A'), OutputInterface::VERBOSITY_VERBOSE);

        $source = $this->getSource($input);

        // Set the crawl global timestamp.
        $timestamp = time();

        // Configure CSV subscriber to capture data.
        $fileToWrite = date('dmY-His') . '-output.csv';
        $csv_logger = new CrawlCsvLoggerSubscriber($fileToWrite);
        $this->eventDispatcher->addSubscriber($csv_logger);

        $this->crawler->crawlSites($source, $this->default_client, $this->default_config, $timestamp, TRUE);
        $this->crawler->determineSiteMapURLs($source, $this->default_client, $this->default_config, $timestamp, 0);

        $sitemapURLs = $this->crawler->getListPendingSitemaps(TRUE);
        $sourceSitemap = new XmlSitemapSource($sitemapURLs);

        // Crawl the sitemaps.
        $this->crawler->crawlSitemaps($sourceSitemap, $this->default_client, $this->default_config, $timestamp, $source->getCurrentIndex());

        $storage = $this->crawler->getStorage();
        $pendingURLs = $storage->getPendingURLs(TRUE);

        if (!empty($pendingURLs)) {
            $pendingSource = new SitesArraySource($pendingURLs);
            $this->crawler->crawlSites($pendingSource, $this->default_client, $this->default_config, $timestamp);
        }

        $output->writeln('Crawling finished. Date: ' . date('l jS \of F Y h:i:s A'), OutputInterface::VERBOSITY_VERBOSE);

        return Command::SUCCESS;
    }

    /**
     * @inheritDoc
     */
    protected function getSource(InputInterface $input) {
        return new CsvSource($input->getArgument('sites_csv_file'));
    }
}
