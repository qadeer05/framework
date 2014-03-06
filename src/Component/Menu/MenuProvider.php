<?php

namespace Pagekit\Component\Menu;

use Pagekit\Component\Menu\Model\FilterManager;
use Pagekit\Component\Menu\Model\MenuInterface;
use Pagekit\Component\Menu\Model\Node;

class MenuProvider implements \IteratorAggregate
{
    /**
     * @var MenuInterface[]
     */
    protected $menus = array();

    /**
     * @var FilterManager
     */
    protected $filters;

    /**
     * Constructor.
     *
     * @param FilterManager $filters
     */
    public function __construct(FilterManager $filters = null)
    {
        $this->filters = $filters ?: new FilterManager;
    }

    /**
     * @return FilterManager
     */
    public function getFilterManager()
    {
        return $this->filters;
    }

    /**
     * Checks whether a menu is registered.
     */
    public function has($id)
    {
        return isset($this->menus[$id]);
    }

    /**
     * Gets a menu.
     *
     * @param  string $id
     * @return MenuInterface
     */
    public function get($id)
    {
        return $this->has($id) ? $this->menus[$id] : null;
    }

    /**
     * Sets a menu.
     *
     * @param MenuInterface $menu
     */
    public function set(MenuInterface $menu)
    {
        $this->menus[$menu->getId()] = $menu;
    }

    /**
     * {@see FilterManager::register}
     */
    public function registerFilter($name, $filter, $priority = 0)
    {
        $this->filters->register($name, $filter, $priority);
    }

    /**
     * Retrieves menu item tree.
     *
     * @param  string $id
     * @param  array  $parameters
     * @return Node
     */
    public function getTree($id, array $parameters = array())
    {
        $menu     = $this->get($id);
        $iterator = $menu->getIterator();

        foreach ($this->filters as $filters) {
            foreach ($filters as $class) {
                $iterator = new $class($iterator, $parameters);
            }
        }

        $items = array(new Node(0));
        foreach ($iterator as $item) {
            $id   = $item->getId();
            $pid  = $item->getParentId();

            if (!isset($items[$id])) {
                $items[$id] = new Node($id);
            }

            $items[$id]->setItem($item);

            if (!isset($items[$pid])) {
                $items[$pid] = new Node($pid);
            }

            $items[$pid]->add($items[$id]);
        }

        return $items[isset($parameters['root'], $items[$parameters['root']]) ? $parameters['root'] : 0];
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->menus);
    }
}