<?php

namespace Pagekit\Framework\Controller;

use Pagekit\Framework\ApplicationAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Controller extends ApplicationAware
{
    /**
     * Returns a redirect response.
     *
     * @param  string  $url
     * @param  array   $parameters
     * @param  int     $status
     * @param  array   $headers
     * @return RedirectResponse
     */
    public function redirect($url = '', $parameters = array(), $status = 302, $headers = array())
    {
        return new RedirectResponse(self::$app['url']->route($url, $parameters), $status, $headers);
    }
}
