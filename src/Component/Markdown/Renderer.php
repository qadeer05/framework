<?php

namespace Pagekit\Component\Markdown;

/**
 * @copyright Copyright (c) Pagekit, http://pagekit.com
 * @copyright Copyright (c) 2011-2014, Christopher Jeffrey (https://github.com/chjj/)
 */
class Renderer
{
    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
    }

    public function code($code, $lang = null, $escaped = null)
    {
        if ($this->options['highlight']) {

            $out = $this->options['highlight']($code, $lang);

            if ($out != null && $out !== $code) {
                $escaped = true;
                $code    = $out;
            }
        }

        if (!$lang) {
            return '<pre><code>'.($escaped ? $code : htmlspecialchars($code, ENT_QUOTES))."\n</code></pre>";
        }

        return implode('', array(
            '<pre><code class="',
            $this->options['langPrefix'],
            htmlspecialchars($lang, ENT_QUOTES),
            '">',
            ($escaped ? $code :htmlspecialchars($code, ENT_QUOTES)),
            "\n</code></pre>\n"
        ));
    }

    public function blockquote($quote)
    {
        return "<blockquote>\n".$quote."</blockquote>\n";
    }

    public function html($html)
    {
        return $html;
    }

    public function heading($text, $level, $raw="")
    {
        return implode('', array(
            '<h',
            $level,
            ' id="',
            $this->options['headerPrefix'],
            preg_replace('/[^\w]+/m', '-', strtolower($raw)),
            '">',
            $text,
            '</h',
            $level,
            ">\n"
        ));
    }

    public function hr()
    {
        return $this->options['xhtml'] ? "<hr/>\n" : "<hr>\n";
    }

    public function lst($body, $ordered=false)
    {
        $type = $ordered ? 'ol' : 'ul';
        return '<'.$type.">\n".$body.'</'.$type.">\n";
    }

    public function listitem($text)
    {
        return '<li>'.$text."</li>\n";
    }

    public function paragraph($text)
    {
        return '<p>'.$text."</p>\n";
    }

    public function table($header, $body)
    {
        return "<table>\n"
        ."<thead>\n"
        .$header
        ."</thead>\n"
        ."<tbody>\n"
        .$body
        ."</tbody>\n"
        ."</table>\n";
    }

    public function tablerow($content)
    {
        return "<tr>\n".$content."</tr>\n";
    }

    public function tablecell($content, array $flags = array())
    {
        $type = $flags['header'] ? 'th' : 'td';
        $tag = $flags['align']
          ? '<'.$type.' style="text-align:'.$flags['align'].'">'
          : '<'.$type.'>';
        return $tag.$content."</".$type.">\n";
    }

    // span level renderer
    public function strong($text)
    {
        return '<strong>'.$text.'</strong>';
    }

    public function em($text)
    {
        return '<em>'.$text.'</em>';
    }

    public function codespan($text)
    {
        return '<code>'.$text.'</code>';
    }

    public function br()
    {
        return $this->options['xhtml'] ? '<br/>' : '<br>';
    }

    public function del($text)
    {
        return '<del>'.$text.'</del>';
    }

    public function link($href="", $title="", $text="")
    {

        if ($this->options['sanitize']) {

            if (strpos($href, 'javascript:') === 0) {
                return '';
            }
        }

        $out = '<a href="'.$href.'"';

        if ($title) {
            $out .= ' title="'.$title.'"';
        }

        $out .= '>'.$text.'</a>';

        return $out;
    }

    public function image($href="", $title=false, $text="")
    {

        $out = '<img src="'.$href.'" alt="'.$text.'"';

        if ($title) {
            $out .= ' title="'.$title.'"';
        }

        $out .= $this->options['xhtml'] ? '/>' : '>';

        return $out;
    }
}
