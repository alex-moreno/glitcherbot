<?php

namespace ScraperBot\Source;

class SitesArraySource implements SourceInterface {
    private $sites;

    /**
     * SitesJsonSource constructor.
     */
    public function __construct($sites) {
        $this->sites = $sites;
    }

    public function getLinks()
    {
        return $this->sites;
    }

    public function addLink($url)
    {
        $this->sites[] = $url;
    }

}
