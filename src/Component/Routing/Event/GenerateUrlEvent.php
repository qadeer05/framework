<?php

namespace Pagekit\Component\Routing\Event;

use Pagekit\Component\Routing\UrlGenerator;
use Symfony\Component\EventDispatcher\Event;

class GenerateUrlEvent extends Event
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $urlParts;

    /**
     * @var UrlGenerator
     */
    protected $generator;

    /**
     * Constructs an event.
     *
     * @param string $url
     * @param UrlGenerator $generator
     */
    public function __construct($url, UrlGenerator $generator)
    {
        $this->url       = $url;
        $this->generator = $generator;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if ($this->urlParts) {
            $this->url = $this->buildUrl($this->urlParts);
        }

        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
        $this->urlParts = null;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->parseUrl('path');
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->parseUrl('path');
        $this->urlParts['path'] = $path;
    }

    /**
     * @return string $path
     */
    public function getPathInfo()
    {
        $path    = $this->parseUrl('path');
        $baseUrl = $this->generator->getContext()->getBaseUrl();

        if (null !== $baseUrl && false === $path = substr($path, strlen($baseUrl))) {
            return '/';
        }

        return $path;
    }

    /**
     * @param string $pathInfo
     */
    public function setPathInfo($pathInfo)
    {
        $this->parseUrl('path');
        $this->urlParts['path'] = $this->generator->getContext()->getBaseUrl().'/'.trim($pathInfo, '/');
    }

    /**
     * @param  string $component
     * @return string
     */
    protected function parseUrl($component = null)
    {
        if (!$this->urlParts) {
            $this->urlParts = parse_url($this->url);
        }

        if (is_string($component)) {
            return isset($this->urlParts[$component]) ? $this->urlParts[$component] : null;
        }

        return $this->urlParts;
    }

    /**
     * @param  array $urlParts
     * @return string
     */
    protected function buildUrl(array $urlParts)
    {
        $url  = isset($urlParts['scheme']) ? $urlParts['scheme'].'://' : '';
        $url .= isset($urlParts['user']) ? $urlParts['user'].(isset($urlParts['pass']) ? ':'.$urlParts['pass'] : '').'@' : '';
        $url .= isset($urlParts['host']) ? $urlParts['host'] : '';
        $url .= isset($urlParts['port']) ? ':'.$urlParts['port'] : '';
        $url .= isset($urlParts['path']) ? $urlParts['path'] : '';
        $url .= isset($urlParts['query']) ? '?'.$urlParts['query'] : '';
        $url .= isset($urlParts['fragment']) ? '#'.$urlParts['fragment'] : '';

        return $url;
    }
}
