<?php

namespace ScraperBot\Event;

use Symfony\Contracts\EventDispatcher\Event;

class CrawlCompleteEvent extends Event {

    public const NAME = 'glitcherbot.crawl_complete';

}
