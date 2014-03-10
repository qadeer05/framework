<?php

namespace Pagekit\Component\Routing;

use Pagekit\Component\Routing\Loader\LoaderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Router
{
    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * @var HttpKernelInterface
     */
    protected $kernel;

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @var LoaderInterface
     */
    protected $loader;

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
     * @var UrlAliasManager
     */
    protected $aliases;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $events
     * @param HttpKernelInterface      $kernel
     * @param mixed                    $loader
     */
    public function __construct(EventDispatcherInterface $events, HttpKernelInterface $kernel, LoaderInterface $loader)
    {
        $this->events = $events;
        $this->kernel = $kernel;
        $this->loader = $loader;
        $this->routes = new RouteCollection;
    }

    /**
     * Retrieve the entire route collection.
     *
     * @return RouteCollection
     */
    public function getRoutes()
    {
        return $this->routes;
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
        return $this->routes->get($name);
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
     * Get the current request context.
     *
     * @return RequestContext
     */
    public function getRequestContext()
    {
        if (!$this->context) {
            $this->context = new RequestContext;
        }

        return $this->context;
    }

    /**
     * Get the URL matcher instance.
     *
     * @return UrlMatcher
     */
    public function getUrlMatcher()
    {
        if (!$this->matcher) {
            $this->matcher = new UrlMatcher($this->routes, $this->getRequestContext());
        }

        return $this->matcher;
    }

    /**
     * Get the alias manager instance.
     *
     * @return UrlAliasManager
     */
    public function getUrlAliases()
    {
        if (!$this->aliases) {
            $this->aliases = new UrlAliasManager;
        }

        return $this->aliases;
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
        $route = $this->match($path, $name, $callback);
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
        $route = $this->match($path, $name, $callback);
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
    public function match($path, $name, $callback)
    {
        $route = new Route($path);
        $route->setDefault('_controller', $callback);

        $this->routes->add($name, $route);

        return $route;
    }

    /**
     * Add a controller.
     *
     * @param string $controller
     * @param array  $options
     */
    public function addController($controller, array $options = array())
    {
        $this->routes->addCollection($this->loader->load($controller, $options));
    }

    /**
     * Registers a before filter.
     *
     * Before filters are run before any route has been matched.
     *
     * @param mixed   $callback
     * @param integer $priority
     */
    public function before($callback, $priority = 0)
    {
        $this->events->addListener(KernelEvents::REQUEST, function (GetResponseEvent $event) use ($callback) {

            if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
                return;
            }

            $ret = call_user_func($callback, $event->getRequest());

            if ($ret instanceof Response) {
                $event->setResponse($ret);
            }

        }, $priority);
    }

    /**
     * Registers an after filter.
     *
     * After filters are run after the controller has been executed.
     *
     * @param mixed  $callback
     * @param integer  $priority
     */
    public function after($callback, $priority = 0)
    {
        $this->events->addListener(KernelEvents::RESPONSE, function (FilterResponseEvent $event) use ($callback) {

            if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
                return;
            }

            call_user_func($callback, $event->getRequest(), $event->getResponse());
        }, $priority);
    }

    /**
     * Registers a finish filter.
     *
     * Finish filters are run after the response has been sent.
     *
     * @param mixed   $callback
     * @param integer $priority
     */
    public function finish($callback, $priority = 0)
    {
        $this->events->addListener(KernelEvents::TERMINATE, function (PostResponseEvent $event) use ($callback) {
            call_user_func($callback, $event->getRequest(), $event->getResponse());
        }, $priority);
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

        if (!$routeObj = $this->getRoute($route)) {
            throw new \RuntimeException(sprintf('Route not found. "%s"', $route));
        }

        $attributes = array_replace($routeObj->getDefaults(), (array) $attributes);
        $attributes['_route'] = $route;

        return $this->kernel->handle($this->request->duplicate($query, $request, $attributes), HttpKernelInterface::SUB_REQUEST);
    }
}
