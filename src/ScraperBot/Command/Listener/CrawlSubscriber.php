<?php


namespace ScraperBot\Command\Listener;


use ScraperBot\Event\CrawlInitiatedEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;

class CrawlSubscriber implements EventSubscriberInterface {

    protected $output = NULL;

    public function __construct(OutputInterface $output) {
        $this->output = $output;
    }

    /**
     * Handler for crawl initiated events.
     */
    public function onCrawlInitiated(Event $event) {

        if ($event instanceof  CrawlInitiatedEvent) {
            $urls = $event->getUrls();

            $this->output->writeln("Crawl Initiated");
            //TODO report URLs.
        }

    }

    public static function getSubscribedEvents() {
        return [
          CrawlInitiatedEvent::NAME => 'onCrawlInitiated',
        ];
    }

}
