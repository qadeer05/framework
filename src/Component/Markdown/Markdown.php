<?php

namespace Pagekit\Component\Markdown;

use Pagekit\Component\Markdown\Lexer\BlockLexer;

/**
 * @copyright Copyright (c) Pagekit, http://pagekit.com
 */
class Markdown
{
    protected $lexer;
    protected $parser;
    protected $options;

    protected static $defaults = array(
        'gfm'          => true,
        'tables'       => true,
        'breaks'       => false,
        'pedantic'     => false,
        'sanitize'     => false,
        'smartLists'   => false,
        'silent'       => false,
        'highlight'    => false,
        'langPrefix'   => 'lang-',
        'smartypants'  => false,
        'headerPrefix' => '',
        'xhtml'        => false
    );

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $options = array_merge(static::$defaults, $options);

        if (!isset($options['renderer'])) {
            $options['renderer'] = new Renderer($options);
        }

        $this->options = $options;
        $this->lexer = new BlockLexer($options);
        $this->parser = new Parser($options);
    }

    /**
     * Parses the markdown syntax and returns HTML.
     *
     * @param  string $text
     * @return string
     */
    public function parse($text)
    {
        return $this->parser->parse($this->lexer->lex($text));
    }

    /**
     * Convert special characters to HTML entities.
     *
     * @param  string  $text
     * @param  boolean $encode
     * @return string
     */
    public static function escape($text, $encode = false)
    {

        $text = preg_replace(!$encode ? '/&(?!#?\w+;)/':'/&/', '&amp;', $text);
        $text = str_replace(array('<', '>', '"', '\''), array('&lt;', '&gt;', '&quot;', '&#39;'), $text);

        return $text;
    }
}
