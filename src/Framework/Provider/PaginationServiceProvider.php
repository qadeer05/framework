<?php

namespace Pagekit\Framework\Provider;

use Pagekit\Component\View\Templating\Helper\PaginationHelper;
use Pagekit\Framework\Application;
use Pagekit\Framework\ServiceProviderInterface;

class PaginationServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
    }

    public function boot(Application $app)
    {
        $app['view.engine.php']->set(new PaginationHelper($app['view'], $app['router']));
    }
}
