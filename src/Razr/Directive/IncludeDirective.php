<?php

namespace Pagekit\Razr\Directive;

use Pagekit\Razr\Token;
use Pagekit\Razr\TokenStream;

class IncludeDirective extends Directive
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'include';
    }

    /**
     * @{inheritdoc}
     */
    public function parse(TokenStream $stream, Token $token)
    {
        if ($stream->nextIf('include') && $stream->expect('(')) {
            return sprintf("echo(\$this->render%s)", $this->parser->parseExpression());
        }
    }
}
