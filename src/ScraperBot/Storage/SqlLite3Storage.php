<?php

namespace ScraperBot\Storage;

class SqlLite3Storage implements StorageInterface {

    /**
     * ResultsService constructor.
     * @param string $db
     */
    public function __construct($db = 'railerdb.sqlite3') {
        $this->pdo = new \SQLite3($db);

        $this->pdo->query("CREATE TABLE IF NOT EXISTS sites (
                            timestamp NOT NULL,
                            site_id INTEGER,
                            url TEXT NOT NULL,
                            size INTEGER,
                            statusCode INTEGER,
                            footprint TEXT
       );");
    }

    /**
     * Add results.
     *
     * @param $destination
     * @param $sites
     */
    public function addResult($site_id, $site_url, $size, $status_code, $footprint, $timestamp) {
        date('dmY-His');
        $query = sprintf("INSERT INTO sites (site_id,url, size, statusCode, footprint, timestamp) VALUES(%d,%d,%d,%d,\"%s\",\"%s\")",
            $site_id, $site_url, $size, $status_code, $footprint, $timestamp);
        $this->pdo->query($query);
    }

    /**
     * @return SQLite3Result
     */
    public function getResults() {
        $queryString = sprintf("SELECT * FROM sites ");
        $query = $this->pdo->query($queryString);
        while ($row = $query->fetchArray()) {
            $results[] = $row;
        }

        return $results;
    }

    /**
     * Get different crawls
     *
     * @return mixed
     */
    public function getTimeStamps() {
        $queryString = sprintf("SELECT * FROM sites group by timestamp ");
        $query = $this->pdo->query($queryString);
        while ($row = $query->fetchArray()) {
            $results[] = $row['timestamp'];
        }

        return $results;
    }

    /**
     * Get results for a given date.
     *
     * @param $timestamp
     * @return mixed
     */
    public function getResultsbyTimestamp($timestamp) {
        $queryString = sprintf("SELECT * FROM sites WHERE timestamp = '%s' order by site_id", $timestamp);
        $query = $this->pdo->query($queryString);
        while ($row = $query->fetchArray()) {
            $results[$timestamp][] = $row;
        }

        return $results;
    }

}
