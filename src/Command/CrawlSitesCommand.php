<?php
declare(strict_types=1);

namespace Command;

use GuzzleHttp\Client;
use ScrapperBot\Crawler;use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command to crawl a supplied list of sites.
 *
 * @package Command
 */
class CrawlSitesCommand extends Command {

    protected static $defaultName = 'bot:crawl-sites';

    /**
     * @inheritDoc
     */
    protected function configure() {
        $this
            ->setDescription('Crawls a supplied list of sites to scrape data.')
            ->addArgument('sites_csv_file', InputArgument::REQUIRED, 'Path to the CSV file containing URLs.')
            ->addOption('config_file', null, InputArgument::OPTIONAL, 'Path to the config file', 'config.php')
            ->addOption('destination_folder', null, InputArgument::OPTIONAL, 'Path to the destination folder for results', '.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->output = $output;

        $sitesinCSV = $this->getFilePath($input);
        $destination = $input->getOption('destination_folder');

        // HTTP Client.
        $client = new Client(['defaults' => [
            'verify' => false
        ]]);

        $config_file = $input->getOption('config_file');

        if (!file_exists($config_file)) {
            $output->writeln("<error>Could not locate config file: " . $config_file .  "</error>");
            return Command::FAILURE;
        }

        $output->writeln("Using config file: " . $config_file, OutputInterface::VERBOSITY_VERBOSE);

        $headers = include('config.php');

        $output->writeln("Using headers: " . print_r($headers, TRUE), OutputInterface::VERBOSITY_DEBUG);

        $site_list = $this->getSiteList($sitesinCSV);

        $crawler = new Crawler($headers);
        $output->writeln('Starting crawling. Date: ' . date('l jS \of F Y h:i:s A'), OutputInterface::VERBOSITY_VERBOSE);
        $crawler->crawlSites($site_list, $client);
        $output->writeln('Crawling finished. Date: ' . date('l jS \of F Y h:i:s A'), OutputInterface::VERBOSITY_VERBOSE);

        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @return string|string[]|null
     */
    protected function getFilePath(InputInterface $input) {
        return $input->getArgument('sites_csv_file');
    }

    /**
     * @param $file
     */
    protected function getSiteList($file) {
        $csvManager = new \csvManager();
        $listOfSites = $csvManager->readCsv($file);

        $listOfSites = array_map(
            function($entry) {
                return empty($entry[0]) ? '' : $entry[0];
            },
            $listOfSites
        );

        return $listOfSites;
    }
}
