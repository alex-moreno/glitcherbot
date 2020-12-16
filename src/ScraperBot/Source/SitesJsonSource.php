<?php


namespace ScraperBot\Source;


class SitesJsonSource implements SourceInterface {
    /**
     * SitesJsonSource constructor.
     */
    public function __construct($file) {
        $this->file = $file;
    }

    public function getLinks()
    {
        if (($json = file_get_contents($this->file)) == false) {
            throw new \Exception("<error>Could not open file: " . $this->file . "</error>");
        }

        $data = json_decode($json, TRUE);
        return empty($data['sites']) ? [] : array_keys($data['sites']);
    }

}