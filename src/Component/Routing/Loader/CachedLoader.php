<?php

namespace Pagekit\Component\Routing\Loader;

use Pagekit\Component\Cache\CacheInterface;

class CachedLoader implements LoaderInterface
{
    /**
     * Route loader instance.
     *
     * @var RouteLoader
     */
    protected $loader;

    /**
     * Cached routes.
     *
     * @var array
     */
    protected $routes = array();

    /**
     * Cache instance.
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Cache key.
     *
     * @var string
     */
    protected $cacheKey = 'Routes';

    /**
     * Cache dirty.
     *
     * @var bool
     */
    protected $cacheDirty = false;

    /**
     * Constructor.
     *
     * @param RouteLoader    $loader
     * @param CacheInterface $cache
     */
    public function __construct(RouteLoader $loader, CacheInterface $cache)
    {
        $this->loader = $loader;
        $this->cache  = $cache;

        if ($routes = $this->cache->fetch($this->cacheKey)) {
            $this->routes = $routes;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($controller, array $options = array())
    {
        if ('.php' != substr($controller, -4)) {
            $reflection = new \ReflectionClass($controller);
            $controller = $reflection->getFileName();
        }

        $time = file_exists($controller) ? filemtime($controller) : 0;

        if (isset($this->routes[$controller]) && $this->routes[$controller]['time'] != $time) {
            unset($this->routes[$controller]);
        }

        if (!isset($this->routes[$controller])) {

            $routes = $this->loader->load($controller, $options);
            $time   = isset($time) ? $time : filemtime($controller);

            $this->routes[$controller] = compact('routes', 'time');
            $this->cacheDirty = true;
        }

        return $this->routes[$controller]['routes'];
    }

    /**
     * Writes the cache file.
     */
    public function write()
    {
        if ($this->cacheDirty) {
            $this->cache->save($this->cacheKey, $this->routes);
        }
    }
}
