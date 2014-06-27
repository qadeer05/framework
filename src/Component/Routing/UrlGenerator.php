<?php

namespace Pagekit\Component\Routing;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;

class UrlGenerator extends BaseUrlGenerator implements LinkGeneratorInterface
{
    /**
     * Generates a path relative to the executed script, e.g. "/dir/file".
     */
    const BASE_PATH = 'base';

    /**
     * {@inheritdoc}
     */
    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, array $requiredSchemes = array())
    {
        $url = parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, $requiredSchemes);

        if ($referenceType === self::BASE_PATH) {
            $url = substr($url, strlen($this->context->getBaseUrl()));
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function generateLink($name = '', array $parameters = array())
    {
        if ($fragment = strstr($name, '#')) {
            $name = strstr($name, '#', true);
        }

        if ($query = substr(strstr($name, '?'), 1)) {
            $name = strstr($name, '?', true);
            parse_str($query, $params);
            $parameters = array_merge($params, $parameters);
        }

        return new Link($name, $this->getPathVariables($name), $parameters, $fragment);
    }

    public function getPathVariables($name)
    {
        if (!$route = $this->routes->get($name)) {
            throw new RouteNotFoundException;
        }

        return $route->compile()->getPathVariables();
    }

    public function getDefaults($name)
    {
        if (!$route = $this->routes->get($name)) {
            throw new RouteNotFoundException;
        }

        return $route->getDefaults();
    }
}