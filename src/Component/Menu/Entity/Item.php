<?php

namespace Pagekit\Component\Menu\Entity;

use Pagekit\Component\Menu\Model\Item as AbstractItem;
use Pagekit\Component\Menu\Model\MenuInterface;

abstract class Item extends AbstractItem
{
    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(name="menu_id", type="integer") */
    protected $menuId;

    /** @Column(type="string") */
    protected $name;

    /** @Column(type="string") */
    protected $url;

    /** @Column(type="json_array") */
    protected $data;

    /**
     * @return string
     */
    public function getMenuId()
    {
        return $this->menuId;
    }

    /**
     * @param string $menuId
     */
    public function setMenuId($menuId)
    {
        $this->menuId = $menuId;
    }

    /**
     * @param MenuInterface $menu
     */
    public function setMenu(MenuInterface $menu)
    {
        $this->menu = $menu;
        $this->setMenuId($menu->getId());
    }
}