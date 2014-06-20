<?php

namespace Pagekit\Razr\Directive;

use Pagekit\Razr\Token;
use Pagekit\Razr\TokenStream;

class SetDirective extends Directive
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'set';
    }

    /**
     * @{inheritdoc}
     */
    public function parse(TokenStream $stream, Token $token)
    {
        if ($stream->nextIf('set') && $stream->expect('(')) {

            $out = '';

            while (!$stream->test(T_CLOSE_TAG)) {
                $out .= $stream->next()->getValue();
            }

            return $out;
        }
    }
}
