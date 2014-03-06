<?php

namespace Pagekit\Component\Menu\Event;

use Pagekit\Component\Menu\Model\MenuInterface;
use Symfony\Component\EventDispatcher\Event;

class MenuEvent extends Event
{
    /**
     * @var MenuInterface
     */
    protected $menu;

    /**
     * Constructs an event.
     *
     * @param MenuInterface $menu
     */
    public function __construct(MenuInterface $menu)
    {
        $this->menu = $menu;
    }

    /**
     * Returns the menu for this event.
     *
     * @return MenuInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }
}