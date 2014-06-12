<?php

namespace Pagekit\Component\Routing;

use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;

class UrlGenerator extends BaseUrlGenerator
{
    /**
     * Generates a path relative to the executed script, e.g. "/dir/file".
     */
    const BASE_PATH = 'base';

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        return $this->generateUrl(parent::generate($name, $parameters, $referenceType), $referenceType);
    }

    /**
     * @param  string $url
     * @param  mixed  $referenceType
     * @return string
     */
    public function generateUrl($url, $referenceType)
    {
        if ($referenceType === self::BASE_PATH) {
            $url = substr($url, strlen($this->context->getBaseUrl()));
        }

        return $url;
    }
}