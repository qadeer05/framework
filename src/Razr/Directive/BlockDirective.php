<?php

namespace Pagekit\Razr\Directive;

use Pagekit\Razr\Token;
use Pagekit\Razr\TokenStream;

class BlockDirective extends Directive
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'block';
    }

    /**
     * @{inheritdoc}
     */
    public function parse(TokenStream $stream, Token $token)
    {
        if ($stream->nextIf('block') && $stream->expect('(')) {
            return sprintf("\$this->getExtension('core')->startBlock%s", $this->parser->parseExpression());
        }

        if ($stream->nextIf('endblock')) {
            return "echo(\$this->getExtension('core')->endBlock())";
        }
    }
}
