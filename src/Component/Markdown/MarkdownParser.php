<?php

namespace Pagekit\Component\Markdown;

/**
 * Markdown parser based on Parsedown.
 *
 * @link http://parsedown.org
 */
class MarkdownParser
{
    /**
     * @var \Parsedown
     */
    protected $parser;

    /**
     * Constructor.
     *
     * @param \Parsedown $parser
     */
    public function __construct(\Parsedown $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Parses the markdown syntax and returns HTML.
     *
     * @param  string $text
     * @return string
     */
    public function parse($text)
    {
        return $this->parser->parse($text);
    }
}
