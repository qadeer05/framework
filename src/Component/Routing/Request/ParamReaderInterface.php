<?php

namespace Pagekit\Component\Routing\Request;

interface ParamReaderInterface
{
    /**
     * Read annotations for a given method.
     *
     * @param  \ReflectionMethod $method
     * @return array
     */
    public function read(\ReflectionMethod $method);
}
