<?php

namespace Pagekit\Component\View\Event;

use Symfony\Component\EventDispatcher\Event;

class ActionEvent extends Event
{
    /**
     * @var string
     */
    protected $action;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var string
     */
    protected $content = '';

    /**
     * Constructs an event.
     */
    public function __construct($action, $parameters = array())
    {
        $this->action     = $action;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @param string $content
     */
    public function append($content)
    {
        $this->content .= ($this->content ? PHP_EOL : '') . $content;
    }
}
