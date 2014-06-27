<?php

namespace Pagekit\Component\Routing;

use Pagekit\Component\Routing\Controller\ControllerResolver;
use Pagekit\Component\Routing\Exception\LoaderException;
use Pagekit\Component\Routing\Generator\Dumper\PhpGeneratorDumper;
use Pagekit\Component\Routing\Loader\LoaderInterface;
use Pagekit\Component\Routing\RequestContext as ExtendedRequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherDumper;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class Router implements RequestMatcherInterface, RouterInterface, LinkGeneratorInterface
{
    /**
     * @var HttpKernelInterface
     */
    protected $kernel;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var ControllerResolver
     */
    protected $resolver;

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var RequestContext
     */
    protected $context;

    /**
     * @var UrlMatcher
     */
    protected $matcher;

    /**
     * @var UrlGenerator
     */
    protected $generator;

    /**
     * @var array
     */
    protected $aliases = array();

    /**
     * @var array
     */
    protected $controllers = [];

    /**
     * @var RouteCollection
     */
    protected $callbacks;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * Constructor.
     *
     * @param HttpKernelInterface $kernel
     * @param LoaderInterface     $loader
     * @param ControllerResolver  $resolver
     * @param bool                $debug
     */
    public function __construct(HttpKernelInterface $kernel, LoaderInterface $loader, ControllerResolver $resolver, $debug = false)
    {
        $this->kernel    = $kernel;
        $this->loader    = $loader;
        $this->context   = new ExtendedRequestContext;
        $this->callbacks = new RouteCollection;
        $this->resolver  = $resolver;
        $this->debug     = $debug;
    }

    /**
     * Gets a route by name.
     *
     * @param string $name The route name
     *
     * @return Route|null A Route instance or null when not found
     */
    public function getRoute($name)
    {
        return $this->getRouteCollection()->get($name);
    }

    /**
     * Get the current route loader.
     *
     * @return LoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Get the current request.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
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
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    /**
     * Gets the URL matcher instance.
     *
     * @return UrlMatcher
     */
    public function getUrlMatcher()
    {
        if (null !== $this->matcher) {
            return $this->matcher;
        }

        $file = sprintf('app://app/cache/urlmatcher_%s.php', $this->getCacheKey());

        if (!file_exists($file)) {
            file_put_contents($file, (new PhpMatcherDumper($this->getRouteCollection()))->dump());
        }

        require_once($file);

        return $this->matcher = new \ProjectUrlMatcher($this->context);
    }

    /**
     * Gets the UrlGenerator instance associated with this Router.
     *
     * @return UrlGenerator
     */
    public function getUrlGenerator()
    {
        if (null !== $this->generator) {
            return $this->generator;
        }

        $file = sprintf('app://app/cache/urlgenerator_%s.php', $this->getCacheKey());

        if (!file_exists($file)) {
            file_put_contents($file, (new PhpGeneratorDumper($this->getRouteCollection()))->dump(array('base_class' => 'Pagekit\Component\Routing\UrlGenerator')));
        }

        require_once($file);

        return $this->generator = new \ProjectUrlGenerator($this->context);
    }

    /**
     * Maps a GET request to a callable.
     *
     * @param  string $path
     * @param  string $name
     * @param  mixed  $callback
     * @return Route
     */
    public function get($path, $name, $callback)
    {
        $route = $this->map($path, $name, $callback);
        $route->setMethods('GET');

        return $route;
    }

    /**
     * Maps a POST request to a callable.
     *
     * @param  string $path
     * @param  string $name
     * @param  mixed  $callback
     * @return Route
     */
    public function post($path, $name, $callback)
    {
        $route = $this->map($path, $name, $callback);
        $route->setMethods('POST');

        return $route;
    }

    /**
     * Maps a path to a callable.
     *
     * @param  string $path
     * @param  string $name
     * @param  mixed  $callback
     * @return Route
     */
    public function map($path, $name, $callback)
    {
        $route = new Route($path);
        $route->setDefault('_controller', '::'.$name);

        $this->resolver->addCallback($name, $callback);
        $this->callbacks->add($name, $route);

        return $route;
    }

    /**
     * Adds a controller.
     *
     * @param string $controller
     * @param array  $options
     */
    public function addController($controller, array $options = array())
    {
        $this->controllers[$controller] = $options;
    }

    /**
     * Adds an alias.
     *
     * @param string   $path
     * @param string   $source
     * @param callable $inbound
     * @param callable $outbound
     */
    public function addAlias($path, $source, callable $inbound = null, callable $outbound = null)
    {
        $path = preg_replace('/^[^\/]/', '/$0', $path);

        $this->aliases[$source] = array($path, $inbound, $outbound);
    }

    /**
     * Aborts the current request by sending a proper HTTP error.
     *
     * @param int $code
     * @param string $message
     * @param array $headers
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function abort($code, $message = '', array $headers = array())
    {
        if ($code == 404) {
            throw new NotFoundHttpException($message);
        } else {
            throw new HttpException($code, $message, null, $headers);
        }
    }

    /**
     * Terminates a request/response cycle.
     *
     * @param Request  $request
     * @param Response $response
     */
    public function terminate(Request $request, Response $response)
    {
        $this->kernel->terminate($request, $response);
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * @param  Request $request
     * @param  int     $type
     * @param  bool    $catch
     * @return Response
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $this->request = $request;
        $this->context->fromRequest($request);

        return $this->kernel->handle($request, $type, $catch);
    }

    /**
     * Handles a Subrequest to call an action internally.
     *
     * @param  string $route
     * @param  array  $query
     * @param  array  $request
     * @param  array  $attributes
     * @throws \RuntimeException
     * @return Response
     */
    public function call($route, array $query = null, array $request = null, array $attributes = null)
    {
        if (empty($this->request)) {
            throw new \RuntimeException('No Request set.');
        }

        $defaults = $this->getUrlGenerator()->getDefaults($route);

        $attributes = array_replace($defaults, (array) $attributes);
        $attributes['_route'] = $route;

        return $this->kernel->handle($this->request->duplicate($query, $request, $attributes), HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        $params = $this->getUrlMatcher()->matchRequest($request);

        if (false !== $pos = strpos($params['_route'], '?')) {
            $params['_route'] = substr($params['_route'], 0, $pos);
        }

        if (isset($params['_route']) and $alias = $this->getAlias($params['_route']) and is_callable($alias[1])) {
            $params = call_user_func($alias[1], $params);
        }

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        return $this->getUrlMatcher()->match($pathinfo);
    }

    /**
     * {@inheritdoc}
     */
    public function generateLink($name = '', array $parameters = array())
    {
        return $this->getUrlGenerator()->generateLink($name, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        if (!$this->routes) {

            $controllers  = new RouteCollection;
            foreach ($this->controllers as $controller => $options) {
                try {
                    $controllers->addCollection($this->loader->load($controller, $options));
                } catch (LoaderException $e) {}
            }

            $aliases = new RouteCollection;
            foreach ($this->aliases as $source => $alias) {

                $name = $source;
                $params = array();
                if ($query = substr(strstr($source, '?'), 1)) {
                    $name = strstr($source, '?', true);
                    parse_str($query, $params);
                }

                if ($route = $controllers->get($name)) {
                    $aliases->add($source, new Route($alias[0], array_merge($route->getDefaults(), array_intersect_key($params, array_flip($route->compile()->getPathVariables())))));
                }

            }

            $this->routes = new RouteCollection;
            $this->routes->addCollection($controllers);
            $this->routes->addCollection($aliases);
            $this->routes->addCollection($this->callbacks);
        }

        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        $generator  = $this->getUrlGenerator();
        $link       = $this->generateLink($name, $parameters);
        $parameters = $link->getParameters();

        try {

            if ($alias = $this->getAlias($link->getName()) and is_callable($alias[2])) {
                $parameters = call_user_func($alias[2], $parameters);
            }

            return $generator->generate($link->getLink(), array_diff_key($parameters, $link->getPathParameters()), $referenceType) . $link->getFragment();

        } catch (RouteNotFoundException $e) {

            return $generator->generate($link->getName(), $parameters, $referenceType). $link->getFragment();

        }
    }

    /**
     * @param  string $name
     * @return array
     */
    protected function getAlias($name)
    {
        return isset($this->aliases[$name]) ? $this->aliases[$name] : false;
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        $key = '';
        foreach (array_keys($this->controllers) as $controller) {
            $key .= $controller;

            if ($this->debug) {
                $key .= file_exists($controller) ? filemtime($controller) : 0;
            }
        }

        foreach (array_keys($this->aliases) as $source => $alias) {
            $key .= $source . $alias[0];
        }

        foreach ($this->callbacks as $name => $route) {
            $key .= $name . $route->getPath();
        }

        return sha1($key);
    }
}
