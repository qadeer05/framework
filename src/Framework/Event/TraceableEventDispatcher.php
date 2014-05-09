<?php

namespace Pagekit\Framework\Event;

use Pagekit\Component\Profiler\TraceableEventDispatcher as BaseDispatcher;

class TraceableEventDispatcher extends BaseDispatcher
{
    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string $event
     * @param mixed  $callback
     * @param int    $priority
     */
    public function on($event, $callback, $priority = 0)
    {
        $this->addListener($event, $callback, $priority);
    }

    /**
     * Dispatches an event to all registered listeners.
     *
     * @param  string $eventName
     * @param  mixed  $event
     * @return Event
     */
    public function trigger($eventName, $event = null)
    {
        if (is_array($event)) {
            $event = new Event($event);
        }

        return $this->dispatch($eventName, $event);
    }
}
