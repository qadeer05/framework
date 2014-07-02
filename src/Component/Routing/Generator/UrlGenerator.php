<?php

namespace Pagekit\Component\Routing\Generator;

use Pagekit\Component\Routing\AliasCollection;
use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;

class UrlGenerator extends BaseUrlGenerator
{
    /**
     * Generates a link url.
     */
    const LINK_URL = 'link';

    /**
     * Generates a path relative to the executed script, e.g. "/dir/file".
     */
    const BASE_PATH = 'base';

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
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        if ($query = substr(strstr($name, '?'), 1)) {
            parse_str($query, $params);
            $name = strstr($name, '?', true);
            $parameters = array_replace($parameters, $params);
        }

        return parent::generate($name, $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, array $requiredSchemes = array())
    {
        if ($params = array_intersect_key($parameters, array_flip($variables))) {

            $link = $name.'?'.http_build_query($params);

            if ($properties = $this->getRouteProperties($link)) {
                $name = $link;
                list($variables, $defaults, $requirements, $tokens, $hostTokens, $requiredSchemes) = $properties;
            }
        }

        if ($referenceType === self::LINK_URL) {
            return $name;
        }

        if ($alias = $this->aliases->get($name) and is_callable($alias[2])) {
            $parameters = call_user_func($alias[2], $parameters);
        }

        $url = parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, $requiredSchemes);

        if ($referenceType === self::BASE_PATH) {
            $url = substr($url, strlen($this->context->getBaseUrl()));
        }

        return $url;
    }

    /**
     * Gets the properties of a route (e.g. requirements).
     *
     * @param  string $name
     * @return array
     */
    protected function getRouteProperties($name)
    {
        if ($route = $this->routes->get($name)) {

            $compiled = $route->compile();

            return array($compiled->getVariables(), $route->getDefaults(), $route->getRequirements(), $compiled->getTokens(), $compiled->getHostTokens(), $route->getSchemes());
        }
    }
}
