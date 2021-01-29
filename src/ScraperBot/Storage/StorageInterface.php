<?php

namespace ScraperBot\Storage;

interface StorageInterface {

    public function addResult($site_id, $site_url, $size, $status_code, $footprint, $timestamp);
    public function addTagDistribution($site_url, $tags, $timestamp);
    public function getResults();
    public function getTimeStamps();
    public function getResultsbyTimestamp($timestamp);
    public function getCrawlDiffs($timestamp1, $timestamp2);
    public function getStatsByStatus($statusCode);
    public function addSitemapURL($url, $index, $timestamp);
    public function getSitemapURLs();
    public function addPendingURL($url, $index, $timestamp);
    public function getPendingURLs();
    public function getStatusCodeTotals($timestamp);

}
