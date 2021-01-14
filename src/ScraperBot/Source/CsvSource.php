<?php

namespace ScraperBot\Source;

use ScraperBot\CsvManager;

class CsvSource implements SourceInterface {

    private $listOfSites = NULL;

    /**
     * CsvSource constructor.
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Get links in the csv.
     *
     * @return array|string[]
     */
    public function getLinks()
    {
        $csvManager = new CsvManager();
        $listOfSites = $csvManager->readCsv($this->file);

        $listOfSites = array_map(
            function($entry) {
                return empty($entry[0]) ? '' : $entry[0];
            },
            $listOfSites
        );

        $this->listOfSites = $listOfSites;

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
        return $this->listOfSites;
    }

    /**
     * Get current index.
     *
     * @return int
     */
    public function getCurrentIndex() {
        return sizeof($this->listOfSites);
    }
}
