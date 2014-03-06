<?php

namespace Pagekit\Component\View\Templating\Helper;

use Pagekit\Component\Markdown\MarkdownParser;
use Symfony\Component\Templating\Helper\Helper;

class MarkdownHelper extends Helper
{
    protected $parser;

    /**
     * Constructor.
     *
     * @param MarkdownParser $parser
     */
    public function __construct(MarkdownParser $parser)
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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'markdown';
    }
}
