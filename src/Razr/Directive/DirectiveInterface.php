<?php

namespace Pagekit\Razr\Directive;

use Pagekit\Razr\Engine;
use Pagekit\Razr\Token;
use Pagekit\Razr\TokenStream;

interface DirectiveInterface
{
    /**
     * Gets the name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the engine.
     *
     * @param $engine
     */
    public function setEngine(Engine $engine);

    /**
     * Parses a directive.
     *
     * @param  TokenStream $stream
     * @param  Token       $token
     * @return string
     */
    public function parse(TokenStream $stream, Token $token);
}
