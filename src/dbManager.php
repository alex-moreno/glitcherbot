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

    public function readDB() {
        date('dmY-His');
        $query = sprintf("SELECT * FROM sites");
        $this->pdo->query($query);

        

    }
}
