<?php

namespace Pagekit\Component\View\Annotation;

/**
 * @Annotation
 */
class View
{
    /**
     * @var string
     */
    protected $template;

    /**
     * @var string
     */
    protected $layout;

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {

            if ($key == 'value') {
                $key = 'template';
            }

            if (!method_exists($this, $method = 'set'.$key)) {
                throw new \BadMethodCallException(sprintf("Unknown property '%s' on annotation '%s'.", $key, get_class($this)));
            }

            $this->$method($value);
        }
    }

    /**
     * Gets the template.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Sets the template.
     *
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Gets the layout template.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Sets the layout template.
     *
     * @param string $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }
}
