<?php

namespace Pagekit\Component\Routing;

use Pagekit\Component\File\Exception\InvalidArgumentException;
use Pagekit\Component\File\ResourceLocator;
use Pagekit\Component\Routing\Event\GenerateRouteEvent;
use Pagekit\Component\Routing\Event\GenerateUrlEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;

class UrlGenerator extends BaseUrlGenerator
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var ResourceLocator
     */
    protected $locator;

    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * @var string
     */
    protected $base;

    /**
     * Constructor.
     *
     * @param Router                   $router
     * @param ResourceLocator          $locator
     * @param EventDispatcherInterface $events
     * @param LoggerInterface          $logger
     */
    public function __construct(Router $router, ResourceLocator $locator, EventDispatcherInterface $events, LoggerInterface $logger = null)
    {
        parent::__construct($router->getRoutes(), $router->getRequestContext(), $logger);

        $this->router  = $router;
        $this->locator = $locator;
        $this->events  = $events;
    }

    /**
     * Get the base URL for the current request.
     *
     * @param  mixed $referenceType
     * @return string
     */
    public function base($referenceType = self::ABSOLUTE_PATH)
    {
        $url = $this->getRequest()->getBasePath();

        if ($referenceType === self::ABSOLUTE_URL) {
            $url = $this->getRequest()->getSchemeAndHttpHost().$url;
        }

        return $url;
    }

    /**
     * Get the current URL for the request.
     *
     * @param  mixed $referenceType
     * @return string
     */
    public function current($referenceType = self::ABSOLUTE_PATH)
    {
        if ($qs = $this->getRequest()->getQueryString()) {
            $qs = '?'.$qs;
        }

        return $this->base($referenceType).$this->getRequest()->getPathInfo().$qs;
    }

    /**
     * Get the URL for the previous request.
     *
     * @param  mixed  $referenceType
     * @return string
     */
    public function previous($referenceType = self::ABSOLUTE_PATH)
    {
        if ($referer = $this->getRequest()->headers->get('referer')) {
            return $this->to($referer, array(), $referenceType);
        }

        return '';
    }

    /**
     * Generate a URL to the given path or named route.
     *
     * @param  string $path
     * @param  mixed  $parameters
     * @param  mixed  $referenceType
     * @return string
     */
    public function to($path, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        if (filter_var($path, FILTER_VALIDATE_URL) !== false) {

            try {

                $path = $this->locator->findResource($path);

            } catch (InvalidArgumentException $e) {
                return $path;
            }

        }

        if (0 === strpos($path, '@')) {
            return $this->route($path, $parameters, $referenceType);
        }

        if ($this->isAbsolutePath($path)) {
            $path = str_replace('\\', '/', $path);
            $path = strpos($path, $base = $this->getBasePath()) === 0 ? substr($path, strlen($base)) : $path;
        }

        if ($query = http_build_query($parameters, '', '&')) {
            $query = '?'.$query;
        }

        return $this->dispatchEvent($this->base($referenceType).'/'.trim($path, '/').$query);
    }

    /**
     * Get the URL to a named route.
     *
     * @param  string $name
     * @param  mixed  $parameters
     * @param  mixed  $referenceType
     * @return string|false
     */
    protected function route($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        try {

            if (false !== strpos($name, '?')) {
                list($name, $query) = explode('?', $name);
                parse_str($query, $params);
                $parameters = array_merge($params, $parameters);
            }

            $url = $this->dispatchEvent($this->generate($name, $parameters, $referenceType));

        } catch (RouteNotFoundException $e) {
            $url = false;
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        $this->events->dispatch('route.generate', $event = new GenerateRouteEvent($name, $parameters));

        return parent::generate($event->getRoute(), $event->getParameters(), $referenceType);
    }

    /**
     * Returns current request.
     *
     * @return string
     */
    protected function getRequest()
    {
        if (($request = $this->router->getRequest()) == null) {
            throw new \RuntimeException('Accessed request outside of request scope.');
        }

        return $request;
    }

    /**
     * Returns the script's base path.
     *
     * @return string
     */
    protected function getBasePath()
    {
        if (!$this->base) {
            $this->base = $this->getRequest()->server->get('SCRIPT_FILENAME');
            $this->base = str_replace('\\', '/', dirname(realpath($this->base)));
        }

        return $this->base;
    }

    /**
     * Dispatches the URL generate event.
     *
     * @param  string $url
     * @return string|false
     */
    protected function dispatchEvent($url)
    {
        $this->events->dispatch('url.generate', $event = new GenerateUrlEvent($url, $this));

        return $event->getUrl();
    }

    /**
     * Returns whether the file path is an absolute path.
     *
     * @param  string $file
     * @return bool
     */
    protected function isAbsolutePath($file)
    {
        return $file && ($file[0] == '/' || $file[0] == '\\' || (strlen($file) > 3 && ctype_alpha($file[0]) && $file[1] == ':' && ($file[2] == '\\' || $file[2] == '/')));
    }
}
