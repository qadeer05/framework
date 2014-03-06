<?php

namespace Pagekit\Component\Routing\Request\Annotation;

/**
 * @Annotation
 */
class Request
{
    /**
     * The parameters.
     *
     * @var array
     */
    public $params = array();

    /**
     * The options.
     *
     * @var array
     */
    public $options = array();

    /**
     * Constructor.
     *
     * @param array $data An array of key/value parameters.
     * @throws \BadMethodCallException
     */
    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $data['parameters'] = $data['value'];
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
     * Returns the parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->params;
    }

    /**
     * Sets the parameters.
     *
     * @param array $params
     */
    public function setParameters($params)
    {
        $this->params = $params;
    }

    /**
     * Returns the options.
     *
     * @param  string $name
     * @return array
     */
    public function getOptions($name = '')
    {
        if ($name) {
            return isset($this->options[$name]) ? $this->options[$name] : array();
        }

        return $this->options;
    }

    /**
     * Sets the options.
     *
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }
}
