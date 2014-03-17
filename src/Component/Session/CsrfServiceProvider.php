<?php

namespace Pagekit\Component\Session;

use Pagekit\Framework\Application;
use Pagekit\Framework\ServiceProviderInterface;
use Pagekit\Component\Session\Csrf\EventListener\CsrfListener;
use Pagekit\Component\Session\Csrf\Provider\SessionCsrfProvider;

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
