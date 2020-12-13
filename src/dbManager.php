<?php

class dbManager {

    /**
     * dbManager constructor.
     * @param string $db
     */
    public function __construct($db = 'railerdb.sqlite3')
    {
        $this->pdo = new SQLite3($db);
    }

    /**
     * Write array into destination csv
     * @param $destination
     * @param $sites
     */
    public function writedb($sites, $timestamp) {
        date('dmY-His');
        $query = sprintf("INSERT INTO sites (site_id,url, size, statusCode, footprint, timestamp) VALUES(%d,%d,%d,%d,\"%s\",\"%s\")",
            $sites['url'],$sites['url'],$sites['size'],$sites['statusCode'],$sites['footprint'], $timestamp);
        $this->pdo->query($query);
    }

    /**
     * @return SQLite3Result
     */
    public function readDB() {
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
    public function getRestultsbyDate($timestamp) {
        $queryString = sprintf("SELECT * FROM sites WHERE timestamp = '%s' order by site_id", $timestamp);
        $query = $this->pdo->query($queryString);
        while ($row = $query->fetchArray()) {
            $results[$timestamp][] = $row;
        }

        return $results;
    }
}
