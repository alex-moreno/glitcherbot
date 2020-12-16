<?php

namespace ScraperBot;

class CsvManager {

    /**
     * Read csv and return an array.
     *
     * @param $source
     * @return mixed
     */
    public function readCsv($source) {
        $file = fopen($source, 'r');
        while (($line = fgetcsv($file)) !== FALSE) {
            //$line is an array of the csv elements
            $array_from_csv[] = $line;
        }
        fclose($file);

        return $array_from_csv;
    }

    /**
     * Write array into destination csv
     * @param $destination
     * @param $sites
     */
    public function writeCsv($outputCSV, $sites, $folder = 'csv') {
        $timestamp = date('dmY-His');

        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $fp = fopen($folder . '/' . $timestamp . '-' . $outputCSV, 'w');
        foreach ($sites as $site) {
            fputcsv($fp, $site);
        }

    }


    /**
     * Write array into destination csv
     * @param $destination
     * @param $sites
     */
    public function writeCsvLine($array, $outputCSV, $folder = 'csv') {
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $fp = fopen($folder . '/' . $outputCSV, 'a');
        fputcsv($fp, $array);
    }

}
