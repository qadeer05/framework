<?php

namespace Pagekit\Component\Routing\Annotation;

/**
 * @Annotation
 */
class Method
{
    /**
     * An array of restricted HTTP methods.
     *
     * @var array
     */
    protected $methods = array();

    /**
     * Constructor.
     *
     * @param array $data An array of key/value parameters.
     */
    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $data['methods'] = $data['value'];
            unset($data['value']);
        }

        foreach ($data as $key => $value) {

            if (!method_exists($this, $method = 'set'.$key)) {
                throw new \BadMethodCallException(sprintf("Unknown property '%s' on annotation '%s'.", $key, get_class($this)));
            }

            $this->$method($value);
        }
    }

    /**
     * Returns the array of HTTP methods.
     *
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Sets the HTTP methods.
     *
     * @param array|string $methods An HTTP method or an array of HTTP methods
     */
    public function setMethods($methods)
    {
        $this->methods = is_array($methods) ? $methods : array($methods);
    }
}
