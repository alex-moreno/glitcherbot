<?php


namespace Command\Acquia;


use Command\CrawlSitesCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
            ->addOption('destination_folder', null, InputArgument::OPTIONAL, 'Path to the destination folder for results', '.');
    }

    /**
     * Get the path to the file containing URLs.
     */
    protected function getFilePath(InputInterface $input) {
        return $input->getArgument('sites_json_file');
    }

    /**
     * Get the list of sites to query.
     */
    protected function getSiteList($file) {
        if (($json = file_get_contents($file)) == false) {
            $this->output->writeln("<error>Could not open file: " . $file . "</error>");
            return [];
        }

        $data = json_decode($json, TRUE);
        return empty($data['sites']) ? [] : array_keys($data['sites']);
    }

}