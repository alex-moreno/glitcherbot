<?php

namespace ScraperBot\Storage;

interface StorageInterface {

    public function addResult($site_id, $site_url, $size, $status_code, $footprint, $timestamp);
    public function getResults();
    public function getTimeStamps();
    public function getResultsbyTimestamp($timestamp);

}