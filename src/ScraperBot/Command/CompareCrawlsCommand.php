<?php
declare(strict_types=1);

namespace ScraperBot\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command to crawl a supplied list of sites.
 *
 * @package Command
 */
class CompareCrawlsCommand extends Command {

    protected static $defaultName = 'bot:compare-crawls';

    /**
     * @inheritDoc
     */
    protected function configure() {
        $this
            ->setDescription('Compare two Crawls for differences.')
            ->addArgument('sites_csv_file', InputArgument::REQUIRED, 'Path to the CSV with the result of the first crawl.')
            ->addArgument('sites_csv_file2', InputArgument::REQUIRED, 'Path to the second CSV with the result of the first crawl.')
            ->addArgument('tolerance', InputArgument::OPTIONAL, 'Tolerance to errors.')
            ->addOption('destination_folder', null, InputArgument::OPTIONAL, 'Path to the destination folder for results', '.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->output = $output;

        $resultsinCSV1 = $input->getArgument('sites_csv_file');
        $resultsinCSV2 = $input->getArgument('sites_csv_file2');
        $tolerance = $input->getArgument('tolerance');

        $listOfDifferences = $this->getCrawlResults($resultsinCSV1, $resultsinCSV2, $tolerance);
        $this->StoreCrawlDifferences($listOfDifferences);

        $crawlSites = new CrawlSitesCommand();

        echo 'crawling';

        return Command::SUCCESS;
    }

    /**
     * @param $listOfDifferences
     */
    public function StoreCrawlDifferences($listOfDifferences) {
        $file = fopen('differences.csv', 'w');
        foreach ($listOfDifferences as $array) {
            fputcsv($file, $array);
        }
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
    protected function getCrawlResults($source1, $source2, $tolerance) {

        $sites1 = $this->ListOfSites($source1);
        $sites2 = $this->ListOfSites($source2);

        $counter = 0;
        foreach ($sites1 as $index => $site) {
            if (sizeof($sites2) >= $index) {
                $threshold = abs($site[2] - $sites2[$index][2]);
                if (($threshold > $tolerance) || $site[1] != $sites2[$index][1]) {
                    $counter++;

                    $list_differences[$index]['site'] = $index;
                    $list_differences[$index]['size_crawl1'] = $site[2];
                    $list_differences[$index]['size_crawl2'] = $sites2[$index][2];
                    $list_differences[$index]['Status_crawl1'] = $site[1];
                    $list_differences[$index]['Status_crawl2'] = $sites2[$index][1];
                }
            }
        }
        echo 'Differences found: ' . $counter;

        return $list_differences;
    }

    public function ListOfSites($source) {
        $file = fopen($source, 'r');
        while (($line = fgetcsv($file)) !== FALSE) {
            //$line is an array of the csv elements
            $array_site[$line[0]] = $line;
        }

        return $array_site;
    }


}
