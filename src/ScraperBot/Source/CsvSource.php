<?php

namespace ScraperBot\Source;

use ScraperBot\Core\GlitcherBot;

/**
 * A CSV file as a source of URLs.
 *
 * @package ScraperBot\Source
 */
class CsvSource implements SourceInterface {

    private array $listOfSites = [];
    private $file = NULL;

    /**
     * CsvSource constructor.
     */
    public function __construct($file) {
        $this->file = $file;
    }

    /**
     * Get links in the csv.
     *
     * @return array|string[]
     */
    public function getLinks() {
        if ($this->listOfSites == NULL) {
            $csvManager = GlitcherBot::service('glitcherbot.csv_manager');
            $listOfSites = $csvManager->readCsv($this->file);

            $listOfSites = array_map(
                function ($entry) {
                    return empty($entry[0]) ? '' : $entry[0];
                },
                $listOfSites
            );

            $this->listOfSites = $listOfSites;
        }

        return $listOfSites;
    }

    /**
     * Add a new url to the array.
     *
     * @param $url
     */
    public function addLink($url) {
        $this->listOfSites[] = $url;
    }

    /**
     * Return all links in the array.
     *
     * @return |null
     */
    public function readLinks() {
        return $this->getLinks();
    }

    /**
     * Get current index.
     *
     * @return int
     */
    public function getSize() {
        return sizeof($this->listOfSites);
    }
}
