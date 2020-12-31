<?php


namespace ScraperBot\Source;

/**
 * Class XmlSitemapSource
 * @package ScraperBot\Source
 */
class XmlSitemapSource implements SourceInterface {

    private $sitemap = NULL;

    /**
     * XmlSitemapSource constructor.
     */
    public function __construct($sitemap = NULL) {
        $this->sitemap = $sitemap;
    }

    public function getLinks() {
        $urls = [];

        if (is_array($this->sitemap)) {
            foreach ($this->sitemap as $sitemap) {
                $urls[] = $sitemap[1];
            }
        }

        return $urls;
    }

    public function addLink($url)
    {
        $this->sitemap[] = $url;
    }

    public function extractLinks($sitemap) {
        $links = [];

        $xml = simplexml_load_string($sitemap);
        // Ensure the string returned is a valid xml.
        if ($xml !== false) {
            foreach ($xml->{'url'} as $item) {
                $links[] =  (string)$item->loc;
            }
        }

        return $links;
    }
}
