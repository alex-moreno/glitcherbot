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
    public function __construct($sitemap) {
        $this->sitemap = $sitemap;
    }

    public function getLinks() {
        $links = [];

        $xml = simplexml_load_file($this->sitemap);

        if($xml->getName() == 'urlset') {
            $children = $xml->children();

            foreach($children as $child) {
                if($child->getName() == 'url') {
                    // Strip the scheme from the URL.
                    $url = $child->loc;
                    $url = preg_replace('#^http(s)?://#', '', rtrim($url,'/'));

                    $links[] = $url;
                }
            }
        }

        return $links;
    }

}
