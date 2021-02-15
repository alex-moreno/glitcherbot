<?php


namespace ScraperBot\Event;


use Symfony\Contracts\EventDispatcher\Event;

class CrawledEvent extends Event {

    public const NAME = 'glitcherbot.crawled';

    private $crawldata = NULL;

    public function __construct($crawldata) {
        $this->crawldata = $crawldata;
    }

    /**
     * @return null
     */
    public function getUrl() {
        return isset($this->crawldata['url']) ? $this->crawldata['url'] : NULL;
    }

}
