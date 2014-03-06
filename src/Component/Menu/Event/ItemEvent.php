<?php

namespace Pagekit\Component\Menu\Event;

use Pagekit\Component\Menu\Model\ItemInterface;
use Symfony\Component\EventDispatcher\Event;

class ItemEvent extends Event
{
    /**
     * @var ItemInterface
     */
    protected $item;

    /**
     * Constructs an event.
     *
     * @param ItemInterface $item
     */
    public function __construct(ItemInterface $item)
    {
        $this->item = $item;
    }

    /**
     * Returns the menu item for this event.
     *
     * @return ItemInterface
     */
    public function getItem()
    {
        return $this->item;
    }
}