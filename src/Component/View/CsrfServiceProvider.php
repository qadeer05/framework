<?php

namespace Pagekit\Component\View;

use Pagekit\Component\View\Csrf\EventListener\CsrfListener;
use Pagekit\Component\View\Csrf\Provider\SessionCsrfProvider;
use Pagekit\Framework\Application;
use Pagekit\Framework\ServiceProviderInterface;

class CsrfServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['csrf'] = function($app) {
            return new SessionCsrfProvider($app['session']);
        };
    }

    public function boot(Application $app)
    {
        $app['events']->addSubscriber(new CsrfListener($app['csrf']));
    }
}
