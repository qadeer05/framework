<?php

namespace Pagekit\Framework\Controller;

use Pagekit\Framework\ApplicationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Controller implements \ArrayAccess
{
    use ApplicationTrait;

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
        return new RedirectResponse($this['url']->to($url, $parameters), $status, $headers);
    }
}
