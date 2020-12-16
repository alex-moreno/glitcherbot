<?php


namespace ScraperBot\Source;


use ScraperBot\CsvManager;

class CsvSource implements SourceInterface {


    /**
     * CsvSource constructor.
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

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

        return $listOfSites;
    }

}
