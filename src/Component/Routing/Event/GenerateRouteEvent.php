<?php

namespace Pagekit\Component\Routing\Event;

use Symfony\Component\EventDispatcher\Event;

class GenerateRouteEvent extends Event
{
    protected $route;
    protected $parameters;

    /**
     * Constructor.
     *
     * @param string $route
     * @param array  $parameters
     */
    public function __construct($route = null, array $parameters = array())
    {
        $this->route      = $route;
        $this->parameters = $parameters;
    }

    /**
     * Getter for route name property.
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Setter for route name property.
     *
     * @param string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * Getter for route parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Setter for route parameters.
     *
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
}
