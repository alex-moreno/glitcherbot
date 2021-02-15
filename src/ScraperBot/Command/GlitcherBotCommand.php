<?php


namespace ScraperBot\Command;


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
    public function __construct(string $name = null, EventDispatcher $eventDispatcher = NULL) {
        parent::__construct($name);

        $this->eventDispatcher = $eventDispatcher;

        // Ensure that glitcher bot commands always have an event dispatcher available
        if ($this->eventDispatcher == NULL) {
            $this->eventDispatcher = new EventDispatcher();
        }
    }

}
