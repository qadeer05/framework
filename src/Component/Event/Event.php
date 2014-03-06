<?php

namespace Pagekit\Component\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;

class Event extends BaseEvent
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * Constructor.
     *
     * @param array $parameters
     */
    public function __construct($parameters = null)
    {
        $this->parameters = $parameters ?: array();
    }

    /**
     * Gets or sets a parameter value.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return mixed|null
     */
    public function __invoke($key, $value = null)
    {
        if ($value !== null) {
            $this->parameters[$key] = $value;
        } elseif (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }
    }
}
