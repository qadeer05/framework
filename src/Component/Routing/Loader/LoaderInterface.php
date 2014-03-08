<?php

namespace Pagekit\Component\Routing\Loader;

interface LoaderInterface
{
    /**
     * Loads a routes collection by parsing controller method names.
     *
     * @param  string $controller
     * @param  array  $options
     * @return RouteCollection
     */
    public function load($controller, array $options = array());
}
