<?php

namespace Pagekit\Component\Routing;

use Symfony\Component\Routing\Route;

class Link
{
    protected $route;
    protected $name;
    protected $parameters;
    protected $fragment;

    /**
     * Constructor.
     *
     * @param Route  $route
     * @param string $name
     * @param array  $parameters
     * @param string $fragment
     */
    public function __construct(Route $route, $name, array $parameters = array(), $fragment = '')
    {
        $this->route      = $route;
        $this->name       = $name;
        $this->parameters = $parameters;
        $this->fragment   = $fragment;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @param string $fragment
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;
    }

    /**
     * @return array
     */
    public function getPathParameters()
    {
        return array_intersect_key($this->getParameters(), array_flip($this->route->compile()->getPathVariables()));
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->name . (($params = $this->getPathParameters()) ? '?' . http_build_query($params) : '');
    }
}
