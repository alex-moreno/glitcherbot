<?php

namespace ScraperBot\MySqlStorage\Plugin\Storage;

class MySqlStorage implements \ScraperBot\Storage\Plugin\Type\StorageInterface {

    public function addResult($site_id, $site_url, $size, $status_code, $footprint, $timestamp)
    {
        // TODO: Implement addResult() method.
    }

    public function addTagDistribution($site_url, $tags, $timestamp)
    {
        // TODO: Implement addTagDistribution() method.
    }

    public function getResults()
    {
        // TODO: Implement getResults() method.
    }

    public function getTimeStamps()
    {
        // TODO: Implement getTimeStamps() method.
    }

    public function getResultsbyTimestamp($timestamp)
    {
        // TODO: Implement getResultsbyTimestamp() method.
    }

    public function getCrawlDiffs($timestamp1, $timestamp2)
    {
        // TODO: Implement getCrawlDiffs() method.
    }

    public function getStatsByStatus($statusCode)
    {
        // TODO: Implement getStatsByStatus() method.
    }

    public function addSitemapURL($url, $index, $timestamp)
    {
        // TODO: Implement addSitemapURL() method.
    }

    public function getSitemapURLs()
    {
        // TODO: Implement getSitemapURLs() method.
    }

    public function addPendingURL($url, $index, $timestamp)
    {
        // TODO: Implement addPendingURL() method.
    }

    public function getPendingURLs()
    {
        // TODO: Implement getPendingURLs() method.
    }

    public function getStatusCodeTotals($timestamp)
    {
        // TODO: Implement getStatusCodeTotals() method.
    }

}
