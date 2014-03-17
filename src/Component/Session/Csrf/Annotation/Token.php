<?php

namespace Pagekit\Component\Session\Csrf\Annotation;

/**
 * @Annotation
 */
class Token
{
    /**
     * The CSRF token name.
     *
     * @var string
     */
    protected $name = '_csrf';

    /**
     * Constructor.
     *
     * @param array $data An array of key/value parameters.
     */
    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $data['name'] = $data['value'];
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
     * Returns the CSRF token name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the CSRF token name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
