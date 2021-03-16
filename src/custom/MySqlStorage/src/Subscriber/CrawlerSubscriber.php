<?php

namespace ScraperBot\MySqlStorage\Subscriber;

use ScraperBot\Event\CrawledEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Example subscriber.
 *
 * Can be registered by providing a '*.subscribers.yaml' file.
 *
 * @see example.mysql.subscribers.yaml
 *
 * @package ScraperBot\MySqlStorage\Subscriber
 */
class CrawlerSubscriber implements EventSubscriberInterface {

    public function onCrawled($event) {
        // Do some work.
    }

    public static function getSubscribedEvents() {
        return [
            CrawledEvent::NAME => 'onCrawled',
        ];
    }

}