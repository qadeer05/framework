<?php

namespace Pagekit\Component\View\Position;

use Pagekit\Component\View\Widget\WidgetProvider;

interface PositionRendererInterface
{
    /**
     * @param  string         $position
     * @param  WidgetProvider $provider
     * @param  \ArrayObject   $widgets
     * @param  array          $options
     * @return string
     */
    public function render($position, WidgetProvider $provider, \ArrayObject $widgets, array $options = array());
}
