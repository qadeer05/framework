<?php

namespace Pagekit\Component\Routing;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouteCollection;

class LinkGenerator
{
    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * Constructor.
     *
     * @param RouteCollection $routes
     */
    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @param  string $name
     * @param  array  $parameters
     * @throws RouteNotFoundException
     * @return bool|Link
     */
    public function generate($name = '', array $parameters = array())
    {
        if ($fragment = strstr($name, '#')) {
            $name = strstr($name, '#', true);
        }

        if ($query = substr(strstr($name, '?'), 1)) {
            $name = strstr($name, '?', true);
            parse_str($query, $params);
            $parameters = array_merge($params, $parameters);
        }

        if (!$route = $this->routes->get($name)) {
            throw new RouteNotFoundException();
        }

        return new Link($route, $name, $parameters, $fragment);
    }
}
