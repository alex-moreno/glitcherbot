<?php


namespace ScraperBot\Command\Subscriber;


use ScraperBot\Core\GlitcherBot;
use ScraperBot\CsvManager;
use ScraperBot\Event\CrawledEvent;
use ScraperBot\Event\CrawlRejectedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listens for Crawler events and logs to CSV file.
 *
 * @package ScraperBot\Command\Subscriber
 */
class CrawlCsvLoggerSubscriber implements EventSubscriberInterface {

    private $csv_file = 'log.csv';
    private $csvManager = NULL;

    /**
     * CrawlCsvLoggerSubscriber constructor.
     */
    public function __construct($csv_file) {
        $this->csv_file = $csv_file;
        $this->csvManager = GlitcherBot::service('glitcherbot.csv_manager');
    }

    /**
     * @param CrawledEvent $event
     */
    public function onCrawled(CrawledEvent $event) {
        $crawldata = $event->getRawCrawldata();
        $this->csvManager->writeCsvLine($crawldata, $this->csv_file);
    }

    public function onCrawlRejected(CrawlRejectedEvent $event) {
        $crawldata = $event->getRawCrawldata();
        $this->csvManager->writeCsvLine($crawldata, $this->csv_file);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents() {
        return [
            CrawledEvent::NAME => 'onCrawled',
            CrawlRejectedEvent::NAME => 'onCrawlRejected',
        ];
    }


}