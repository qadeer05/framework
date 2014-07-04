<?php

namespace Pagekit\Framework\Templating\Helper;

use Pagekit\Component\View\ViewInterface;
use Symfony\Component\Templating\Helper\Helper;

class ActionHelper extends Helper
{
    /**
     * @var ViewInterface
     */
    protected $view;

    /**
     * Constructor.
     *
     * @param ViewInterface $view
     */
    public function __construct(ViewInterface $view)
    {
        $this->view = $view;
    }

    /**
     * Registers an action callback
     *
     * @param string $name
     * @param array  $parameters
     */
    public function call($name, $parameters = array())
    {
        echo $this->view->callAction($name, $parameters);
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'action';
    }
}
