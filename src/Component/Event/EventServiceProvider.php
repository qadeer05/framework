<?php

namespace Pagekit\Component\Event;

use Pagekit\Framework\Application;
use Pagekit\Framework\ServiceProviderInterface;

class EventServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['events'] = function() {
            return new EventDispatcher;
        };
    }

    public function boot(Application $app)
    {
    }
}
