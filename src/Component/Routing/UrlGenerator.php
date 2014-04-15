<?php

namespace Pagekit\Component\Routing;

use Pagekit\Component\File\Exception\InvalidArgumentException;
use Pagekit\Component\File\ResourceLocator;
use Pagekit\Component\Routing\Event\GenerateRouteEvent;
use Pagekit\Component\Routing\Event\GenerateUrlEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;

class UrlGenerator extends BaseUrlGenerator
{
    /**
     * Generates a base relative URL, e.g. "dir/file".
     */
    const RELATIVE_URL = 'base';

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
        parent::__construct($router->getRoutes(), $router->getContext(), $logger);

        $this->router  = $router;
        $this->locator = $locator;
        $this->events  = $events;
    }

    /**
     * Get the base path for the current request.
     *
     * @param  mixed $referenceType
     * @return string
     */
    public function base($referenceType = self::ABSOLUTE_PATH)
    {
        $url = $this->context->getBasePath();

        if ($referenceType === self::ABSOLUTE_URL) {
            $url = $this->context->getSchemeAndHttpHost().$url;
        }

        return $url;
    }

    /**
     * Get the URL for the current request.
     *
     * @param  mixed $referenceType
     * @return string
     */
    public function current($referenceType = self::ABSOLUTE_PATH)
    {
        $url = $this->context->getBaseUrl();

        if ($referenceType === self::ABSOLUTE_URL) {
            $url = $this->context->getSchemeAndHttpHost().$url;
        }

        if ($qs = $this->context->getQueryString()) {
            $qs = '?'.$qs;
        }

        return $url.$this->context->getPathInfo().$qs;
    }

    /**
     * Get the URL for the previous request.
     *
     * @return string
     */
    public function previous()
    {
        if ($referer = $this->context->getReferer()) {
            return $this->to($referer);
        }

        return '';
    }

    /**
     * Get the URL to a path or locator resource.
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

        if ($this->isAbsolutePath($path)) {
            $path = str_replace('\\', '/', $path);
            $path = strpos($path, $base = $this->context->getScriptPath()) === 0 ? substr($path, strlen($base)) : $path;
        }

        if ($query = http_build_query($parameters, '', '&')) {
            $query = '?'.$query;
        }

        return $this->generateUrl($this->base($referenceType).'/'.trim($path, '/').$query, $referenceType);
    }

    /**
     * Get the URL to a route path or name route.
     *
     * @param  string $path
     * @param  mixed  $parameters
     * @param  mixed  $referenceType
     * @return string|false
     */
    public function route($path = '', $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        if (filter_var($path, FILTER_VALIDATE_URL) !== false || $this->isAbsolutePath($path)) {
            return $path;
        }

        try {

            if (strpos($path, '?') !== false) {
                list($name, $query) = explode('?', $path);
                parse_str($query, $params);
                $params = array_merge($params, $parameters);
            } else {
                $name = $path;
                $params = $parameters;
            }

            $url = $this->generate($name, $params, $referenceType);

        } catch (RouteNotFoundException $e) {

            if (strpos($path, '@') === 0) {
                return false;
            } elseif ($path !== '') {
                $path = "/$path";
            }

            $url = $this->context->getBaseUrl().$path;
        }

        return $this->generateUrl($url, $referenceType);
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
     * @param  string $url
     * @param  mixed  $referenceType
     * @return string
     */
    protected function generateUrl($url, $referenceType)
    {
        $this->events->dispatch('url.generate', $event = new GenerateUrlEvent($url, $this));

        $url = $event->getUrl();

        if ($referenceType === self::RELATIVE_URL) {
            $url = ltrim(substr($url, strlen($this->context->getBaseUrl())), '/');
        }

        return $url;
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
