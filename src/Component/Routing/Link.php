<?php

namespace Pagekit\Component\Routing;

class Link
{
    protected $name;
    protected $parameters;
    protected $variables;
    protected $fragment;

    /**
     * Constructor.
     *
     * @param string     $name
     * @param array      $parameters
     * @param array      $variables
     * @param string     $fragment
     */
    public function __construct($name, $variables = array(), array $parameters = array(), $fragment = '')
    {
        $this->name       = $name;
        $this->parameters = $parameters;
        $this->variables  = $variables;
        $this->fragment   = $fragment;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @param string $fragment
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;
    }

    /**
     * @return array
     */
    public function getPathParameters()
    {
        return array_intersect_key($this->getParameters(), array_flip($this->variables));
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->name . (($params = $this->getPathParameters()) ? '?' . http_build_query($params) : '');
    }
}
