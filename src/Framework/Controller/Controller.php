<?php

namespace Pagekit\Framework\Controller;

use Pagekit\Framework\ApplicationAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Controller extends ApplicationAware
{
    /**
     * Generate a URL to the given path or named route.
     *
     * @param  string $path
     * @param  mixed  $parameters
     * @param  mixed  $referenceType
     * @return string
     */
    public function url($path, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return self::$app['url']->to($path, $parameters, $referenceType);
    }

    /**
     * Returns a redirect response.
     *
     * @param  string  $url
     * @param  array   $parameters
     * @param  int     $status
     * @param  array   $headers
     * @return RedirectResponse
     */
    public function redirect($url, $parameters = array(), $status = 302, $headers = array())
    {
        return new RedirectResponse(self::$app['url']->to($url, $parameters), $status, $headers);
    }
}
