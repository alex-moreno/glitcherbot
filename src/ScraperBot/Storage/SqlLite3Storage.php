<?php

namespace ScraperBot\Storage;

class SqlLite3Storage implements StorageInterface {

    private $pdo;

    /**
     * ResultsService constructor.
     * @param string $db
     */
    public function __construct($db = 'glitcherbot.sqlite3') {
        $this->pdo = new \SQLite3($db);

        $this->pdo->query("CREATE TABLE IF NOT EXISTS sites (
                            timestamp NOT NULL,
                            site_id INTEGER,
                            url TEXT NOT NULL,
                            size INTEGER,
                            statusCode INTEGER,
                            footprint TEXT
       );");

        $this->pdo->query("CREATE TABLE IF NOT EXISTS sitemapURLs (
                            timestamp NOT NULL,
                            site_id INTEGER,
                            url TEXT NOT NULL
       );");

        $this->pdo->query("CREATE TABLE IF NOT EXISTS pendingURLs (
                            timestamp NOT NULL,
                            site_id INTEGER,
                            url TEXT NOT NULL
       );");

        $this->pdo->query("CREATE TABLE IF NOT EXISTS tags (
                            timestamp NOT NULL,
                            url TEXT NOT NULL,
                            tag_name TEXT NOT NULL,
                            tag_value TEXT NOT NULL
                            
       );");

    }

    /**
     * Add results.
     *
     * @param $destination
     * @param $sites
     */
    public function addResult($site_id, $site_url, $size, $status_code, $footprint, $timestamp) {
        $query = sprintf("INSERT INTO sites (site_id, url, size, statusCode, footprint, timestamp) VALUES(%d,\"%s\",%d,%d,\"%s\",\"%s\")",
            $site_id, $site_url, $size, $status_code, $footprint, $timestamp);
        $this->pdo->query($query);
    }

    /**
     * Store tags found.
     *
     * @param $site_url
     * @param $tagDistribution
     * @param $timestamp
     */
    public function addTagDistribution($site_url, $tagDistribution, $timestamp) {
        if (is_array($tagDistribution)) {
            foreach ($tagDistribution as $index=>$tag) {
                $query = sprintf("INSERT INTO tags (url, timestamp, tag_name, tag_value) VALUES(\"%s\",\"%s\",\"%s\",\"%s\")",
                    $site_url, $timestamp, $index, $tag);
                $this->pdo->query($query);
            }
        }
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
    public function getTimeStamps($timestamps = NULL, $getLatest = NULL) {
        if ($timestamps != NULL) {
            $queryString = sprintf("SELECT DISTINCT * FROM sites WHERE timestamp = '%s' OR timestamp = '%s' GROUP BY timestamp order by url", strtotime($timestamps['date1']), strtotime($timestamps['date2']));
        } else {
            $queryString = sprintf("SELECT DISTINCT * FROM sites GROUP by timestamp");
            if ($getLatest!=NULL && $getLatest == 'true') {
                // Get only the two latest crawls.
                $queryString = sprintf("SELECT DISTINCT * FROM sites GROUP by timestamp order by timestamp desc LIMIT 2");
            }
        }

        $query = $this->pdo->query($queryString);
        while ($row = $query->fetchArray()) {
            $results[] = $row['timestamp'];
        }

        return $results;
    }

    /**
     * Get different crawls
     *
     * @return mixed
     */
    public function getStatusCodes() {
        $queryString = sprintf("SELECT statusCode FROM sites group by StatusCode");
        $query = $this->pdo->query($queryString);
        while ($row = $query->fetchArray()) {
            $results[] = $row['statusCode'];
        }

        return $results;
    }

    /**
     * Get results for a given date.
     *
     * @param $timestamp
     * @param null $onlyLatest
     * @param null $site
     *  Site for which we want to fetch results.
     * @return mixed
     */
    public function getResultsbyTimestamp($timestamp, $onlyLatest = NULL, $site = NULL) {
        if ($onlyLatest != NULL) {

        }

        // If site is empty we'll list all sites instead.
        if ($site == NULL) {
            $site = "%";
        } else {
            $siteTags = $this->getTags($site, $timestamp);
        }

        $queryString = sprintf("SELECT * FROM sites INNER JOIN tags ON sites.url=tags.url WHERE sites.timestamp='%s' AND tag_name='total' AND sites.timestamp=tags.timestamp AND sites.url LIKE '%s';", $timestamp, $site);

        $query = $this->pdo->query($queryString);
        while ($row = $query->fetchArray()) {
            $results[$timestamp][$row['url']] = $row;
            // Get tags.
            $results[$timestamp][$row['url']]['tags'] = $siteTags;
            $results[$timestamp][$row['url']]['tags']['total'] = $row['tag_value'];
        }

        return $results;
    }

    /**
     * Return tags linked to a given site and timestamp.
     *
     * @param $site
     * @param $timestamp
     *
     * @return mixed
     */
    public function getTags($site, $timestamp) {
        $queryString = sprintf("SELECT * FROM tags WHERE timestamp='%s' AND url LIKE '%s';", $timestamp, $site);
        $query = $this->pdo->query($queryString);

        $tags = [];
        while ($row = $query->fetchArray()) {
            $tags[$row['tag_name']] = $row['tag_value'];
        }
        return $tags;
    }

    /**
     * Get results for a given date.
     *
     * @param $timestamp
     * @return mixed
     */
    public function getResultsAndTagsbyTimestamp($timestamp, $onlyLatest = NULL) {
        if ($onlyLatest != NULL) {

        }

//        $queryString = sprintf("SELECT * FROM sites WHERE timestamp = '%s' order by url", $timestamp);
        $queryString = sprintf("SELECT * FROM sites  INNER JOIN tags ON sites.url=tags.url WHERE sites.timestamp='%s' AND tag_name='total';", $timestamp);

        $query = $this->pdo->query($queryString);
        while ($row = $query->fetchArray()) {
            $results[$timestamp][$row['url']] = $row;
            // Get tags.
            $results[$timestamp][$row['url']]['tags']['total'] = $row['tag_value'];

        }

        return $results;
    }

    /**
     * Get diffs between two crawls.
     *
     * @param $timestamp1
     * @param $timestamp2
     */
    public function getCrawlDiffs($timestamp1, $timestamp2, $tolerance = 1000) {
        $queryString = sprintf("SELECT DISTINCT * FROM sites INNER JOIN tags ON sites.url=tags.url WHERE sites.timestamp = '%s' AND tag_name='total' order by site_id", $timestamp1);
        $results1 = $this->pdo->query($queryString);

        $queryString2 = sprintf("SELECT DISTINCT * FROM sites INNER JOIN tags ON sites.url=tags.url WHERE sites.timestamp = '%s' AND tag_name='total' order by site_id", $timestamp2);
        $results2 = $this->pdo->query($queryString2);

        $listofSites1 = array();
        $index = 1;
        while ($row = $results1->fetchArray()) {
            $listofSites1[$index] = $row;
            $index++;
        }

        $index2 = 1;
        $naughtySite = [];
        while ($row2 = $results2->fetchArray()) {
            $listofSites2[$index2] = $row2;
            $diff = abs($listofSites1[$index2]['size'] - $row2['size']);
            if (($diff > $tolerance && $diff > 0) ) { // || ($listofSites1[$index2]['statusCode'] != $row2['statusCode'])
                $naughtySite[$index2]['size1'][$index2] = $listofSites1[$index2]['size'];
                $naughtySite[$index2]['statusCode1'][$index2] = $listofSites1[$index2]['statusCode'];
                $naughtySite[$index2]['url1'][$index2] = $listofSites1[$index2]['url'];

                $naughtySite[$index2]['size2'][$row2[2]] = $row2['size'];
                $naughtySite[$index2]['statusCode2'][$row2[2]] = $row2['statusCode'];
                $naughtySite[$index2]['url2'][$index2] = $listofSites1[$index2]['url'];
            }
            $index2++;
        }

        return $naughtySite;
    }


    public function getNaughtySites($timestamp1, $timestamp2, $tolerance = 1000) {
        $queryString = sprintf("SELECT DISTINCT * FROM sites INNER JOIN tags ON sites.url=tags.url WHERE sites.timestamp = '%s' AND tag_name='total' order by site_id", $timestamp1);
        $results1 = $this->pdo->query($queryString);

        $queryString2 = sprintf("SELECT DISTINCT * FROM sites INNER JOIN tags ON sites.url=tags.url WHERE sites.timestamp = '%s' AND tag_name='total' order by site_id", $timestamp2);
        $results2 = $this->pdo->query($queryString2);

        $listofSites1 = array();
        $index = 1;
        while ($row = $results1->fetchArray()) {
            $listofSites1[$index] = $row;
            $index++;
        }

        $index2 = 1;
        $naughtySite = [];
        while ($row2 = $results2->fetchArray()) {
            $listofSites2[$index2] = $row2;
            $diff = abs($listofSites1[$index2]['size'] - $row2['size']);
            if (($diff > $tolerance && $diff > 0) || ($listofSites1[$index2]['statusCode'] != $row2['statusCode'])) { //
                $naughtySite[$listofSites1[$index2]['url']] = $listofSites1[$index2]['url'];
//                echo '<br>site:: ' . $listofSites1[$index2]['url'];
            }
            $index2++;
        }

        return $naughtySite;
    }
    /**
     * Get different crawls
     *
     * @return mixed
     */
    public function getStatsByStatus($statusCode) {
        $numRows = [];

        $timeStamps = $this->getTimeStamps();
        foreach ($timeStamps as $timeStamp) {
            $queryString = sprintf("SELECT COUNT(*) as count FROM sites WHERE timestamp = '%s' AND statusCode = '%d'", $timeStamp, $statusCode);
            $rows = $this->pdo->query($queryString);
            $row = $rows->fetchArray();

            $numRows[$timeStamp] = $row['count'];
        }

        return $numRows;
    }

    public function addSitemapURL($url, $index, $timestamp)
    {
        $query = sprintf("INSERT INTO sitemapURLs (timestamp, url, site_id) VALUES(%d,%d,\"%s\")", $timestamp, $index, $url);
        $this->pdo->query($query);
    }

    /**
     * Get sitemaps.
     *
     * @param bool $dumpCurrent
     * @return array
     */
    public function getSitemapURLs($dumpCurrent = FALSE) {
        $results = [];
        $queryString = sprintf("SELECT * FROM sitemapURLs ");
        $query = $this->pdo->query($queryString);

        while ($row = $query->fetchArray()) {
            $results[] = $row;
        }

        if($dumpCurrent) {
            // Remove once has been accessed.
            $this->dumpSitemaps();
        }

        return $results;
    }

    /**
     * Remove sitemap urls from the db
     */
    public function dumpSitemaps() {
        $this->pdo->query("delete from sitemapURLs ");
    }

    /**
     * Insert new pending url into the db.
     *
     * @param $url
     * @param $index
     * @param $timestamp
     */
    public function addPendingURL($url, $index, $timestamp)
    {
        $query = sprintf("INSERT INTO pendingURLs (timestamp, url, site_id) VALUES(%d,\"%s\",%d)", $timestamp, $url, $index);
        $this->pdo->query($query);
    }

    /**
     * Get pending urls from the db.
     *
     * @param bool $dumpCurrent
     * @return mixed
     */
    public function getPendingURLs($dumpCurrent = FALSE)
    {
        $results = [];
        $queryString = sprintf("SELECT url FROM pendingURLs");
        $query = $this->pdo->query($queryString);

        while ($row = $query->fetchArray()) {
            $results[] = $row['url'];
        }

        if ($dumpCurrent) {
            // Remove once has been accessed.
            $this->dumpPendingURLs();
        }

        return $results;
    }

    /**
     * Remove pending urls from the db
     */
    public function dumpPendingURLs() {
        $this->pdo->query("delete from pendingURLs");
    }
}
