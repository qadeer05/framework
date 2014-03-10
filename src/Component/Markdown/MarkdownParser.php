<?php


namespace Pagekit\Component\Markdown;

use Michelf\MarkdownExtra;

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
     * @param MarkdownExtra $parser
     */
    public function __construct(MarkdownExtra $parser)
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
        return $this->parser->transform($text);
    }
}
