<?php

namespace Pagekit\Component\Routing\Matcher;

use Pagekit\Component\Routing\AliasCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher as BaseUrlMatcher;

class UrlMatcher extends BaseUrlMatcher
{
    /**
     * @var AliasCollection
     */
    protected $aliases;

    /**
     * Gets the alias collection.
     *
     * @return AliasCollection
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Sets the alias collection.
     *
     * @param AliasCollection
     */
    public function setAliases(AliasCollection $aliases)
    {
        $this->aliases = $aliases;
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        $params = parent::match($pathinfo);

        if (false !== $pos = strpos($params['_route'], '?')) {
            $params['_route'] = substr($params['_route'], 0, $pos);
        }

        if (isset($params['_route']) and $alias = $this->aliases->get($params['_route']) and is_callable($alias[1])) {
            $params = call_user_func($alias[1], $params);
        }

        return $params;
    }
}
