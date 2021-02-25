<?php

namespace ScraperBot\Source;

class SitesJsonSource implements SourceInterface {

    private $listOfSites = NULL;

    /**
     * SitesJsonSource constructor.
     */
    public function __construct($file) {
        $this->file = $file;
    }

    public function getLinks() {
        if (($json = file_get_contents($this->file)) == false) {
            throw new \Exception("<error>Could not open file: " . $this->file . "</error>");
        }

        $data = json_decode($json, TRUE);
        $this->listOfSites = $data;
        return empty($data['sites']) ? [] : array_keys($data['sites']);
    }

    /**
     * @inheritDoc
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
    public function getCurrentIndex() {
        return sizeof($this->listOfSites);
    }
}