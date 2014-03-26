<?php

namespace Pagekit\Component\View;

use Pagekit\Component\View\Event\ActionEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Templating\DelegatingEngine;
use Symfony\Component\Templating\EngineInterface;

class View
{
    /**
     * @var EngineInterface
     */
    protected $engine;

    /**
     * @var string|false
     */
    protected $layout = false;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * @var string
     */
    protected $prefix = 'view.action.';

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $events
     */
    public function __construct(EventDispatcherInterface $events, EngineInterface $engine = null)
    {
        $this->events = $events;
        $this->engine = $engine ?: new DelegatingEngine;
    }

    /**
     * Get the template engine.
     *
     * @return DelegatingEngine
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Get the layout template.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Set the layout template.
     *
     * @param string $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Gets a parameter.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $array = $this->parameters;

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {

            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Sets a parameter.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $keys = explode('.', $key);
        $array =& $this->parameters;

        while (count($keys) > 1) {

            $key = array_shift($keys);

            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = array();
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;
    }

    /**
     * Render a template.
     *
     * @param  string $name
     * @param  array  $parameters
     *
     * @return string
     */
    public function render($name, array $parameters = array())
    {
        foreach ($parameters as $key => $value) {
            if (strpos($key, '.') !== false) {
                $this->set($key, $value);
            }
        }

        if (!$this->isAbsolutePath($name) and !strpos($name, '://') > 0) {
            $name = 'view://'.$name;
        }

        return $this->engine->render($name, array_replace($this->parameters, $parameters));
    }

    /**
     * Registers an action callback.
     *
     * @param string   $name
     * @param callable $listener
     * @param integer  $priority
     */
    public function addAction($name, $listener, $priority = 0)
    {
        $this->events->addListener($this->prefix.$name, $listener, $priority);
    }

    /**
     * Calls an action callback
     *
     * @param string $action
     * @param array  $parameters
     *
     * @return string The placeholder
     */
    public function callAction($action, $parameters = array())
    {
        $prefix = $this->prefix;
        $placeholder = '<!-- '.uniqid('action.').' -->';

        $this->events->addListener(KernelEvents::RESPONSE, function(FilterResponseEvent $event, $name, $dispatcher) use ($prefix, $action, $parameters, $placeholder) {

            $response = $event->getResponse();
            $replace  = $dispatcher->dispatch($prefix.$action, new ActionEvent($action, $parameters))->getContent();

            $response->setContent(str_replace($placeholder, $replace, $response->getContent()));
        });

        return $placeholder;
    }

    /**
     * Returns whether the file path is an absolute path.
     *
     * @param  string $file
     * @return bool
     */
    protected function isAbsolutePath($file)
    {
        return $file && ($file[0] == '/' || $file[0] == '\\' || (strlen($file) > 3 && ctype_alpha($file[0]) && $file[1] == ':' && ($file[2] == '\\' || $file[2] == '/')));
    }
}
