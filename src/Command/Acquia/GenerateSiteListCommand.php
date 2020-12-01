<?php


namespace Command\Acquia;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Helper command to generate a list of sites to crawl from an Acquia Site Factory sites.json file.
 *
 * @package Command\Acquia
 */
class GenerateSiteListCommand extends Command {

    protected static $defaultName = 'acquia:generate-site-list';

    /**
     * @inheritDoc
     */
    protected function configure() {
        $this
            ->setDescription("Generate a list of sites from an Acquia Site Factory sites.json file.")
            ->addArgument('sites_file', InputArgument::REQUIRED, 'Path to sites.json file.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $input_file = $input->getArgument('sites_file');

        if (($json = file_get_contents($input_file)) == false) {
            $output->writeln("<error>Could not open file: " . $input_file . "</error>");
        }

        $data = json_decode($json, TRUE);

        if (empty($data['sites'])) {
            $output->writeln('<warning>Could not find any sites in json file.</warning>');
        }
        else {
            $fp = fopen('generated_site_list.csv', 'w');

            foreach (array_keys($data['sites']) as $site) {
                fputcsv($fp, [$site]);
            }

            fclose($fp);
        }

        return Command::SUCCESS;
    }

}
