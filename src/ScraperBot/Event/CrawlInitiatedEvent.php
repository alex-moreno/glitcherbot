<?php


namespace ScraperBot\Event;


use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event to represent initiation of a crawl of a set of URLs.
 *
 * @package ScraperBot\Event
 */
class CrawlInitiatedEvent extends Event {

    public const NAME = 'glitcherbot.crawl_initiated';

    public const CRAWL_TYPE_SITE = 'site';
    public const CRAWL_TYPE_SITEMAP = 'sitemap';

    private $urls = NULL;

    private $crawl_type = NULL;

    /**
     * CrawlInitiatedEvent constructor.
     * @param $crawl_type
     * @param null $urls
     */
    public function __construct($crawl_type, $urls = NULL) {
        $this->crawl_type = $crawl_type;
        $this->urls = $urls;
    }

    /**
     * @return null
     */
    public function getUrls() {
        return $this->urls;
    }

    /**
     * @param null $urls
     */
    public function setUrls($urls): void {
        $this->urls = $urls;
    }

    /**
     * @return null
     */
    public function getCrawlType() {
        return $this->crawl_type;
    }

}
