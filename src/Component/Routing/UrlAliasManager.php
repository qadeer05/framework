<?php

namespace Pagekit\Component\Routing;

class UrlAliasManager
{
    /**
     * @var array
     */
    protected $aliases = array();

    /**
     * Register alias
     *
     * @param string $alias
     * @param string $source
     */
    public function register($alias, $source)
    {
        $alias = preg_replace('/^[^\/]/', '/$0', $alias);

        $this->aliases = array($alias => $source) + $this->aliases;
    }

    /**
     * Get alias by source
     *
     * @param  string $source
     * @return string
     */
    public function alias($source)
    {
        return array_search($source, $this->aliases);
    }

    /**
     * Get source by alias
     *
     * @param  string $alias
     * @return string
     */
    public function source($alias)
    {
        return isset($this->aliases[$alias]) ? $this->aliases[$alias] : null;
    }
}
