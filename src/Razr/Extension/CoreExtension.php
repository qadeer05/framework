<?php

namespace Pagekit\Razr\Extension;

use Pagekit\Razr\Engine;
use Pagekit\Razr\Directive\BlockDirective;
use Pagekit\Razr\Directive\ControlDirective;
use Pagekit\Razr\Directive\ExtendDirective;
use Pagekit\Razr\Directive\FunctionDirective;
use Pagekit\Razr\Directive\IncludeDirective;
use Pagekit\Razr\Directive\RawDirective;
use Pagekit\Razr\Directive\SetDirective;
use Pagekit\Razr\Exception\InvalidArgumentException;
use Pagekit\Razr\Exception\LogicException;

class CoreExtension implements ExtensionInterface
{
    protected $blocks = array();
    protected $openBlocks = array();

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'core';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(Engine $engine)
    {
        // directives
        $engine->addDirective(new BlockDirective);
        $engine->addDirective(new ControlDirective);
        $engine->addDirective(new ExtendDirective);
        $engine->addDirective(new IncludeDirective);
        $engine->addDirective(new RawDirective);
        $engine->addDirective(new SetDirective);

        // functions
        $engine->addFunction('e', array($engine, 'escape'));
        $engine->addFunction('escape', array($engine, 'escape'));
        $engine->addFunction('block', array($this, 'block'));
        $engine->addFunction('constant', array($this, 'getConstant'));
        $engine->addFunction('json', 'json_encode');
        $engine->addFunction('upper', 'strtoupper');
        $engine->addFunction('lower', 'strtolower');
        $engine->addFunction('format', 'sprintf');
        $engine->addFunction('replace', 'strtr');
    }

    /**
     * Gets or sets a block.
     *
     * @return string
     */
    public function block($name, $value = null)
    {
        if ($value === null) {
            return isset($this->blocks[$name]) ? $this->blocks[$name] : null;
        }

        $this->blocks[$name] = $value;
    }

    /**
     * Starts a block.
     *
     * @param string $name
     */
    public function startBlock($name)
    {
        if (in_array($name, $this->openBlocks)) {
            throw new InvalidArgumentException(sprintf('A block "%s" is already started.', $name));
        }

        $this->openBlocks[] = $name;

        if (!isset($this->blocks[$name])) {
            $this->blocks[$name] = null;
        }

        ob_start();
        ob_implicit_flush(0);
    }

    /**
     * Stops a block.
     *
     * @return string
     */
    public function endBlock()
    {
        if (!$this->openBlocks) {
            throw new LogicException('No block started.');
        }

        $name  = array_pop($this->openBlocks);
        $value = ob_get_clean();

        if ($this->blocks[$name] === null) {
            $this->blocks[$name] = $value;
        }

        return $this->blocks[$name];
    }

    /**
     * Reset all blocks.
     *
     * @return void
     */
    public function resetBlocks()
    {
        $this->blocks = array();
        $this->openBlocks = array();
    }

    /**
     * Gets a constant from an object.
     *
     * @return mixed
     */
    public function getConstant($name, $object = null)
    {
        if ($object !== null) {
            $name = sprintf('%s::%s', get_class($object), $name);
        }

        return constant($name);
    }
}
