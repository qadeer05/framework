<?php

namespace Pagekit\Component\Routing;

use Pagekit\Component\File\Exception\InvalidArgumentException;
use Pagekit\Component\File\ResourceLocator;
use Pagekit\Component\Routing\Event\GenerateRouteEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouteCollection;

class UrlProvider
{
    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @var RequestContext
     */
    protected $context;

    /**
     * @var ResourceLocator
     */
    protected $locator;

    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * @var UrlGenerator
     */
    private $url;

    /**
     * @var LinkGenerator
     */
    protected $link;

    /**
     * Constructor.
     *
     * @param Router                   $router
     * @param ResourceLocator          $locator
     * @param EventDispatcherInterface $events
     * @param UrlGenerator             $url
     * @param LinkGenerator            $link
     */
    public function __construct(Router $router, ResourceLocator $locator, EventDispatcherInterface $events, UrlGenerator $url = null, LinkGenerator $link = null)
    {
        $this->routes  = $router->getRoutes();
        $this->context = $router->getContext();
        $this->locator = $locator;
        $this->events  = $events;
        $this->url     = $url  ?: new UrlGenerator($this->routes, $this->context);
        $this->link    = $link ?: new LinkGenerator($this->routes);
    }

    /**
     * @return UrlGenerator
     */
    public function getUrlGenerator()
    {
        return $this->url;
    }

    public function getLinkGenerator()
    {
        return $this->link;
    }

    /**
     * Get the base path for the current request.
     *
     * @param  mixed $referenceType
     * @return string
     */
    public function base($referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        $url = $this->context->getBasePath();

        if ($referenceType === UrlGenerator::ABSOLUTE_URL) {
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
    public function current($referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        $url = $this->context->getBaseUrl();

        if ($referenceType === UrlGenerator::ABSOLUTE_URL) {
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
    public function to($path, $parameters = array(), $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        if (0 === strpos($path, '@')) {
            return $this->route($path, $parameters, $referenceType);
        }

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

        return $this->url->generateUrl($this->base($referenceType).'/'.trim($path, '/').$query, $referenceType);
    }

    /**
     * Get the URL to a named route.
     *
     * @param  string $name
     * @param  mixed  $parameters
     * @param  mixed  $referenceType
     * @return string|false
     */
    public function route($name = '', $parameters = array(), $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        try {

            $event = $this->events->dispatch('route.generate', new GenerateRouteEvent($this->link->generate($name, $parameters), $referenceType));

            if ($url = $event->getUrl()) {
                return $url;
            }

            $link = $event->getLink();

            return $this->url->generate($link->getName(), $link->getParameters(), $event->getReferenceType()) . $link->getFragment();

        } catch (RouteNotFoundException $e) {}

        return false;

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
