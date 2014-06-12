<?php

namespace Pagekit\Component\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext as Context;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class UrlAliasManager implements RequestMatcherInterface, UrlGeneratorInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @var UrlMatcher
     */
    protected $matcher;

    /**
     * @var UrlGenerator
     */
    protected $generator;

    /**
     * Constructor.
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context   = $context;
        $this->routes    = new RouteCollection;
        $this->matcher   = new UrlMatcher($this->routes, $context);
        $this->generator = new UrlGenerator($this->routes, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Gets an alias.
     *
     * @param  string $source
     * @return Route
     */
    public function get($source)
    {
        return $this->routes->get($source);
    }

    /**
     * Adds an alias.
     *
     * @param string   $path
     * @param string   $source
     * @param callable $inbound
     * @param callable $outbound
     */
    public function add($path, $source, $inbound = null, $outbound = null)
    {
        $this->routes->add($source, new Route($path, array(), array(), array('_inbound' => $inbound, '_outbound' => $outbound)));
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        try {

            if (!$route = $this->routes->get($name)) {
                return false;
            }

            if ($outbound = $route->getOption(('_outbound'))) {
                $parameters = call_user_func($outbound, $parameters);
            }

            return $this->generator->generate($name, $parameters, $referenceType);

        } catch (\Exception $e) {}

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        if ($parameters = $this->matcher->matchRequest($request)) {

            if ($route = $this->routes->get($parameters['_route'])) {
                if ($inbound = $route->getOption('_inbound')) {
                    $parameters = call_user_func($inbound, $parameters);;
                }
            }

            if ($query = substr(strstr($parameters['_route'], '?'), 1)) {
                $parameters['_route'] = strstr($parameters['_route'], '?', true);
                parse_str($query, $params);
                $parameters = array_merge($parameters, $params);
            }

            return $parameters;
        }
    }
}
