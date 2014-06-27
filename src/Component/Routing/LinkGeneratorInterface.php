<?php

namespace Pagekit\Component\Routing;

use Symfony\Component\Routing\Exception\RouteNotFoundException;

interface LinkGeneratorInterface
{
    /**
     * @param  string $name
     * @param  array  $parameters
     * @throws RouteNotFoundException
     * @return bool|Link
     */
    public function generateLink($name = '', array $parameters = array());
}
