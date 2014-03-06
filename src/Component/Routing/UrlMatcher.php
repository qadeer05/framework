<?php

namespace Pagekit\Component\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher as BaseUrlMatcher;

class UrlMatcher extends BaseUrlMatcher implements RequestMatcherInterface
{
    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        return $this->match($request->attributes->get('_system_path') ?: $request->getPathInfo());
    }
}