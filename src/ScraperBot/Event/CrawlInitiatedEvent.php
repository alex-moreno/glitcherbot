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

    private $urls = NULL;

    public function __construct($urls = NULL) {
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

}
