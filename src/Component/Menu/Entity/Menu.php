<?php

namespace Pagekit\Component\Menu\Entity;

use Pagekit\Component\Menu\Model\Menu as AbstractMenu;

abstract class Menu extends AbstractMenu
{
    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(type="string") */
    protected $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}