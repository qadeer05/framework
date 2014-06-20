<?php

namespace Pagekit\Razr\Directive;

use Pagekit\Razr\Token;
use Pagekit\Razr\TokenStream;

class FunctionDirective extends Directive
{
    protected $function;

    /**
     * Constructor.
     *
     * @param string   $name
     * @param callable $function
     */
    public function __construct($name, $function)
    {
        $this->name = $name;
        $this->function = $function;
    }

    /**
     * Calls the function with an array of arguments.
     *
     * @param  array $args
     * @return mixed
     */
    public function call(array $args = array())
    {
        return call_user_func_array($this->function, $args);
    }

    /**
     * @{inheritdoc}
     */
    public function parse(TokenStream $stream, Token $token)
    {
        if ($stream->nextIf($this->name)) {

            if ($stream->test('(')) {
                $out = sprintf("echo(\$this->escape(\$this->getDirective('%s')->call(array%s)))", $this->name, $this->parser->parseExpression());
            } else {
                $out = sprintf("echo(\$this->escape(\$this->getDirective('%s')->call()))", $this->name);
            }

            return $out;
        }
    }
}
