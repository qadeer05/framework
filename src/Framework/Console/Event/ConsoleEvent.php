<?php

namespace Pagekit\Framework\Console\Event;

use Symfony\Component\EventDispatcher\Event;

class ConsoleEvent extends Event
{
    protected $console;

    /**
     * Constructor.
     */
    public function __construct($console)
    {
        $this->console = $console;
    }

    /**
     * Returns the console application.
     *
     * @return Application
     */
    public function getConsole()
    {
        return $this->console;
    }
}
