<?php


namespace ScraperBot\Command\Subscriber;


use ScraperBot\Event\CrawledEvent;
use ScraperBot\Event\CrawlInitiatedEvent;
use ScraperBot\Event\CrawlRejectedEvent;
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
        if ($event instanceof CrawlInitiatedEvent) {
            $urls = $event->getUrls();

            $this->output->writeln("Crawl Initiated (type: " . $event->getCrawlType() . ")", OutputInterface::VERBOSITY_DEBUG);

            foreach ($urls as $url) {
                $this->output->writeln('URL to be crawled: ' . $url, OutputInterface::VERBOSITY_VERY_VERBOSE);
            }
        }
    }

    public function onCrawled(CrawledEvent $event) {
        $this->output->writeln('Crawled: ' . $event->getUrl());
    }

    public function onCrawlRejected(CrawlRejectedEvent $event) {
        $this->output->writeln('<error>Crawl rejected: ' . $event->getURL() . '</error>');
        $this->output->writeln('<error>Reason: ' . $event->getReason() . '</error>', OutputInterface::VERBOSITY_DEBUG);
    }

    public static function getSubscribedEvents() {
        return [
          CrawlInitiatedEvent::NAME => 'onCrawlInitiated',
          CrawledEvent::NAME => 'onCrawled',
          CrawlRejectedEvent::NAME => 'onCrawlRejected',
        ];
    }

}
