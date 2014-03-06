<?php

namespace Pagekit\Component\View\Templating\Helper;

use Pagekit\Component\Routing\Router;
use Pagekit\Component\View\Pagination\Paginator;
use Pagekit\Component\View\View;
use Symfony\Component\Templating\Helper\Helper;

class PaginationHelper extends Helper
{
    /**
     * @var View
     */
    protected $view;

    /**
     * @var Router
     */
    protected $router;

    /**
     * Constructor.
     *
     * @param View   $view
     * @param Router $router
     */
    public function __construct(View $view, Router $router)
    {
        $this->view = $view;
        $this->router = $router;
    }

    /**
     * Renders the given paginator object
     *
     * @param  Paginator $paginator
     * @param  string    $route
     * @param  array     $params
     * @param  string    $template
     * @return string
     */
    public function render(Paginator $paginator, $route, $params = array(), $template = null)
    {
        if (null === $template) {
            $template = __DIR__.'/../../views/default.blade.php';
        }

        return $this->view->render($template, compact('paginator', 'route', 'params'));
    }

    /**
     * Generates a URL for a given route and page
     *
     * @param  string $route
     * @param  int    $page
     * @param  array  $parameters
     * @return string
     */
    public function path($route, $page, array $parameters = array())
    {
        if (isset($parameters['_page'])) {
            $parameters[$parameters['_page']] = $page;
            unset($parameters['_page']);
        } else {
            $parameters['page'] = $page;
        }

        return $this->router->to($route, $parameters);
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'pagination';
    }
}
