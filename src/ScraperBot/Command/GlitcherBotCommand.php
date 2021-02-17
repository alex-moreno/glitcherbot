<?php


namespace ScraperBot\Command;


use ScraperBot\Core\GlitcherBot;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Base class for glitcher bot commands.
 *
 * @package ScraperBot\Command
 */
class GlitcherBotCommand extends Command {

    protected $eventDispatcher = NULL;

    /**
     * Initialise GlitcherBot commands.
     *
     * @param string|null $name
     * @param EventDispatcher|NULL $eventDispatcher
     */
    public function __construct(string $name = null) {
        parent::__construct($name);
        $this->eventDispatcher = GlitcherBot::service('glitcherbot.event_dispatcher');
    }

}
