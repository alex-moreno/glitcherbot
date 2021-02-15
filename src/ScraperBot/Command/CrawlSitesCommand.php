<?php
declare(strict_types=1);

namespace ScraperBot\Command;

use GuzzleHttp\Client;
use ScraperBot\Command\Listener\CrawlSubscriber;
use ScraperBot\Crawler;
use ScraperBot\CsvManager;
use ScraperBot\Event\CrawlInitiatedEvent;
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

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->output = $output;

        $crawl_subscriber = new CrawlSubscriber($this->output);
        $this->eventDispatcher->addSubscriber($crawl_subscriber);

        // $destination = $input->getOption('destination_folder');
        $use_base_uri = $input->getOption('use_base_uri');

        $default_config = ['defaults' => [
            'verify' => false
        ]];
        // HTTP Client.
        $client = new Client($default_config);

        $config_file = $input->getOption('config_file');

        if (!file_exists($config_file)) {
            $output->writeln("<error>Could not locate config file: " . $config_file .  "</error>");
            return Command::FAILURE;
        }

        $output->writeln("Using config file: " . $config_file, OutputInterface::VERBOSITY_VERBOSE);
        $headers = include('config.php');
        $output->writeln("Using headers: " . print_r($headers, TRUE), OutputInterface::VERBOSITY_DEBUG);

        $sqlStorage = new \ScraperBot\Storage\SqlLite3Storage();
        $crawler = new Crawler($sqlStorage, $headers, $this->eventDispatcher);
        $output->writeln('Starting crawling. Date: ' . date('l jS \of F Y h:i:s A'), OutputInterface::VERBOSITY_VERBOSE);

        // Unless configured, do not ask the crawler to use a base URI.
        if (empty($use_base_uri)) {
            $default_config = NULL;
        }

        $source = $this->getSource($input);

        $timestamp = time();
        $crawler->crawlSites($source, $client, $default_config, $timestamp);
        $crawler->crawlSiteMaps($source, $client, $default_config, $timestamp, 0);

        $sitemapURLs = $crawler->getListPendingSitemaps(TRUE);
        $sourceSitemap = new XmlSitemapSource($sitemapURLs);

        // Crawl the sitemaps.
        $crawler->extractSitemaps($sourceSitemap, $client, $default_config, $timestamp, $source->getCurrentIndex());

        $pendingURLs = $sqlStorage->getPendingURLs(TRUE);
        $pendingSource = new SitesArraySource($pendingURLs);
        $crawler->crawlSites($pendingSource, $client, $default_config, $timestamp);

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
