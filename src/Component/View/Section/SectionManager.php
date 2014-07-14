<?php

namespace Pagekit\Component\View\Section;

class SectionManager
{
    protected $sections     = [];
    protected $openSections = [];
    protected $renderer     = [];
    protected $defaults     = [];

    /**
     * Adds a renderer.
     *
     * @param string   $name
     * @param callable $renderer
     */
    public function addRenderer($name, callable $renderer)
    {
        $this->renderer[$name] = $renderer;
    }

    /**
     * Registers default options for a section.
     *
     * @param string $name
     * @param array  $options
     */
    public function register($name, $options = [])
    {
        $this->defaults[$name] = $options;
    }

    /**
     * Starts a new section.
     *
     * This method starts an output buffer that will be
     * closed when the end() method is called.
     *
     * @param  string $name
     * @param  string $options
     * @throws \InvalidArgumentException
     */
    public function start($name, $options = '')
    {
        if (in_array($name, $this->openSections)) {
            throw new \InvalidArgumentException(sprintf('A section named "%s" is already started.', $name));
        }

        $this->openSections[] = [$name, $options];
        $this->sections[$name] = isset($this->sections[$name]) ? $this->sections[$name] : '';

        ob_start();
        ob_implicit_flush(0);
    }

    /**
     * Ends a section.
     *
     * @throws \LogicException
     */
    public function end()
    {
        if (!$this->openSections) {
            throw new \LogicException('No section started.');
        }

        list($name, $options) = array_pop($this->openSections);

        $section = ob_get_clean();

        switch($options) {
            case 'append':
                $section = $this->sections[$name] . $section;
                break;
            case 'prepend':
                $section .= $this->sections[$name];
        }

        $this->sections[$name] = $section;
    }

    /**
     * Returns true if the section exists.
     *
     * @param  string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->sections[$name]);
    }

    /**
     * Gets the section value.
     *
     * @param  string      $name
     * @param  bool|string $default
     * @return string The section content
     */
    public function get($name, $default = false)
    {
        return isset($this->sections[$name]) ? $this->sections[$name] : $default;
    }

    /**
     * Sets a section value.
     *
     * @param string $name
     * @param mixed  $content
     */
    public function set($name, $content)
    {
        $this->sections[$name] = $content;
    }

    /**
     * Appends to a section.
     *
     * @param string $name
     * @param string $content
     */
    public function append($name, $content)
    {
        if (!isset($this->sections[$name])) {
            $this->set($name, $content);
            return;
        }

        if (is_callable($content)) {
            if (!is_array($this->sections[$name])) {
                $this->sections[$name] = [$this->sections[$name]];
            }
            array_push($this->sections[$name], $content);
            return;
        }

        if (is_array($this->sections[$name])) {
            $this->sections[$name] = array_merge($this->sections[$name], $content);
        }

        if (is_string($this->sections[$name])) {
            $this->sections[$name] .= $content;
        }
    }

    /**
     * Prepends to a section.
     *
     * @param string $name
     * @param string $content
     */
    public function prepend($name, $content)
    {
        if (!isset($this->sections[$name])) {
            $this->set($name, $content);
            return;
        }

        if (is_callable($content)) {
            if (!is_array($this->sections[$name])) {
                $this->sections[$name] = [$this->sections[$name]];
            }
            array_unshift($this->sections[$name], $content);
            return;
        }

        if (is_array($this->sections[$name])) {
            $this->sections[$name] = array_merge((array) $content, $this->sections[$name]);
        }

        if (is_string($this->sections[$name])) {
            $this->sections[$name] = $content . $this->sections[$name];
        }
    }

    /**
     * Renders a section.
     *
     * @param  string $name
     * @param  array  $options
     * @return bool
     */
    public function render($name, $options = [])
    {
        if (!isset($this->sections[$name])) {
            return false;
        }

        $options += isset($this->defaults[$name]) ? $this->defaults[$name] : [];

        $renderer = isset($options['renderer'], $this->renderer[$options['renderer']]) ? $options['renderer'] : false;

        echo $renderer ? $this->renderer[$renderer]($name, $this->sections[$name], $options) : $this->renderDefault($this->sections[$name]);

        return true;
    }

    /**
     * The default renderer.
     *
     * @param  mixed $value
     * @return string
     */
    protected function renderDefault($value)
    {
        if (is_array($value)) {
            return implode('', array_map([$this, 'renderDefault'], $value));
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_callable($value)) {
            return $value();
        }
    }
}
