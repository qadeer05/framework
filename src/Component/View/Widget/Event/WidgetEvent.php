<?php

namespace Pagekit\Component\View\Widget\Event;

use Pagekit\Component\View\Widget\Model\WidgetInterface;
use Symfony\Component\EventDispatcher\Event;

class WidgetEvent extends Event
{
    private $widget;

    /**
     * Constructs an event.
     *
     * @param WidgetInterface $widget
     */
    public function __construct(WidgetInterface $widget)
    {
        $this->widget = $widget;
    }

    /**
     * Returns the widget for this event.
     *
     * @return WidgetInterface
     */
    public function getWidget()
    {
        return $this->widget;
    }
}
