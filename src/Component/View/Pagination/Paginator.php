<?php

namespace Pagekit\Component\View\Pagination;

class Paginator
{
    protected $name;
    protected $total;
    protected $current;
    protected $limit;
    protected $range;
    protected $pages;
    protected $showall = false;

    /**
     * Constructor.
     *
     * @param int $total The total number of items
     * @param int $current The current page (default: 1)
     * @param int $limit The number of items per page (default: 10)
     * @param int $range The range for the displayed page (default: 5)
     * @param string $name The name of the pagination http GET variable
     */
    public function __construct($total, $current = 1, $limit = 10, $range = 5, $name = 'page')
    {
        // init vars
        $this->_name    = $name;
        $this->total   = (int) max($total, 0);
        $this->current = (int) max($current, 1);
        $this->limit   = (int) max($limit, 1);
        $this->range   = (int) max($range, 1);
        $this->pages   = (int) ceil($this->total / $this->limit);

        // check if current page is valid
        if ($this->current > $this->pages) {
            $this->current = $this->pages;
        }
    }

    public function name()
    {
        return $this->_name;
    }

    public function total()
    {
        return $this->total;
    }

    public function current()
    {
        return $this->current;
    }

    public function limit()
    {
        return $this->limit;
    }

    public function range()
    {
        return $this->range;
    }

    public function pages()
    {
        return $this->pages;
    }

    /**
     * Get the show all items flag
     *
     * @return boolean True if we have to show all the items
     *
     * @since 1.0.0
     */
    public function getShowAll()
    {
        return $this->showall || $this->pages < 2;
    }

    /**
     * Set the show all items flag
     *
     * @param boolean $showall If we have to show all the items
     *
     * @since 1.0.0
     */
    public function setShowAll($showall)
    {
        $this->showall = $showall;
    }

    /**
     * Get the current limit start
     *
     * @return int The current limit start
     *
     * @since 1.0.0
     */
    public function limitStart()
    {
        return ($this->current - 1) * $this->limit;
    }

    /**
     * Get the link with the added GET parameters
     *
     * @param string $url The url to which we should add the GET parameter
     * @param mixed $vars A list of variables to add to the url
     *
     * @return string The url with the added GET parameters
     *
     * @since 1.0.0
     */
    public function link($url, $vars)
    {

        if (!is_array($vars)) {
            $vars = array($vars);
        }

        return $url.(strpos($url, '?') === false ? '?' : '&').implode('&', $vars);
    }

    /**
     * Render the pagination
     *
     * @param string $url The url of the page on which we're adding the pagination
     *
     * @return string The html code of the pagination
     *
     * @since 1.0.0
     */
    public function render($url = 'index.php', $layout = null)
    {

        $html = '';

        // check if show all
        if ($this->showall) {
            return $html;
        }

        // check if current page is valid
        if ($this->current > $this->pages) {
            $this->current = $this->pages;
        }

        if ($this->pages > 1) {

            $range_start = max($this->current - $this->range, 1);
            $range_end = min($this->current + $this->range - 1, $this->pages);

            if ($this->current > 1) {
                $link = $url;
                $html .= '<a class="start" href="'.JRoute::_($link).'">&lt;&lt;</a>&nbsp;';
                $link = $this->current - 1 == 1 ? $url : $this->link($url, $this->_name.'='.($this->current - 1));
                $html .= '<a class="previous" href="'.JRoute::_($link).'">&lt;</a>&nbsp;';
            }

            for ($i = $range_start; $i <= $range_end; $i++) {
                if ($i == $this->current) {
                    $html .= '[<span>'.$i.'</span>]';
                } else {
                    $link = $i == 1 ? $url : $this->link($url, $this->_name.'='.$i);
                    $html .= '<a href="'.JRoute::_($link).'">'.$i.'</a>';
                }
                $html .= "&nbsp;";
            }

            if ($this->current < $this->pages) {
                $link = $this->link($url, $this->_name.'='.($this->current + 1));
                $html .= '<a class="next" href="'.JRoute::_($link).'">&gt;&nbsp;</a>&nbsp;';
                $link = $this->link($url, $this->_name.'='.($this->pages));
                $html .= '<a class="end" href="'.JRoute::_($link).'">&gt;&gt;&nbsp;</a>&nbsp;';
            }
        }

        return $html;
    }
}