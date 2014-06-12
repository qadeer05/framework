<?php

namespace Pagekit\Component\Routing\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class GenerateRouteEvent extends Event
{
    protected $url;
    protected $route;
    protected $path;
    protected $parameters;
    protected $referenceType;
    protected $fragment;
    protected $internal;

    /**
     * Constructor.
     *
     * @param RouteCollection $routes
     * @param string          $path
     * @param array           $parameters
     * @param mixed           $referenceType
     */
    public function __construct(RouteCollection $routes, $path = '', array $parameters = array(), $referenceType = false)
    {
        if ($fragment = strstr($path, '#')) {
            $path = strstr($path, '#', true);
        }

        if ($query = substr(strstr($path, '?'), 1)) {
            $path = strstr($path, '?', true);
            parse_str($query, $params);
            $parameters = array_merge($params, $parameters);
        }

        $this->route         = $routes->get($path);
        $this->path          = $path;
        $this->parameters    = $parameters;
        $this->referenceType = $referenceType;
        $this->fragment      = $fragment;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        $this->stopPropagation();
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    public function getPathParameters()
    {
        if (!$this->route) {
            return array();
        }

        return array_intersect_key($this->getParameters(), array_flip($this->route->compile()->getPathVariables()));
    }

    public function getInternal()
    {
        return $this->path . (($params = $this->getPathParameters()) ? '?' . http_build_query($params) : '');
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
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
     * @return bool|mixed
     */
    public function getReferenceType()
    {
        return $this->referenceType;
    }

    /**
     * @param bool|mixed $referenceType
     */
    public function setReferenceType($referenceType)
    {
        $this->referenceType = $referenceType;
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
}
