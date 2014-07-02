<?php

namespace Pagekit\Component\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class AliasCollection
{
    /**
     * @var array
     */
    protected $aliases = array();

    /**
     * Gets an alias.
     *
     * @param  string $name
     * @return array
     */
    public function get($name)
    {
        return isset($this->aliases[$name]) ? $this->aliases[$name] : false;
    }

    /**
     * Adds an alias.
     *
     * @param string   $path
     * @param string   $name
     * @param callable $inbound
     * @param callable $outbound
     */
    public function add($path, $name, callable $inbound = null, callable $outbound = null)
    {
        $path = preg_replace('/^[^\/]/', '/$0', $path);

        $this->aliases[$name] = array($path, $inbound, $outbound);
    }

    /**
     * Gets the route collection.
     *
     * @return RouteCollection
     */
    public function getRoutes(RouteCollection $routes)
    {
        $collection = new RouteCollection;

        foreach ($this->aliases as $source => $alias) {

            $name = $source;
            $params = array();

            if ($query = substr(strstr($source, '?'), 1)) {
                $name = strstr($source, '?', true);
                parse_str($query, $params);
            }

            if ($route = $routes->get($name)) {
                $collection->add($source, new Route($alias[0], array_merge($route->getDefaults(), array_intersect_key($params, array_flip($route->compile()->getPathVariables())))));
            }
        }

        return $collection;
    }

    /**
     * Returns an array of resources to this collection.
     *
     * @return array
     */
    public function getResources()
    {
        $resources = array();

        foreach ($this->aliases as $name => $alias) {
            $resources[] = $name.$alias[0];
        }

        return $resources;
    }
}
