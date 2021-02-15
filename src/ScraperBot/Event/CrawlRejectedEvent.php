<?php


namespace ScraperBot\Event;


use Symfony\Contracts\EventDispatcher\Event;

class CrawlRejectedEvent extends Event {

    public const NAME = 'glitcherbot.crawl_rejected';

    private $data = NULL;

    private $reason = 'UNKNOWN';

    public function __construct($data, $reason) {
        $this->data = $data;
        $this->reason = $reason;
    }

    public function getURL() {
        return empty($this->data['url']) ? NULL : $this->data['url'];
    }

    /**
     * @return string
     */
    public function getReason(): string {
        return $this->reason;
    }

}