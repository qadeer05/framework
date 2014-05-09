<?php

namespace Pagekit\Framework\Provider;

use Pagekit\Framework\Application;
use Pagekit\Framework\Event\EventDispatcher;
use Pagekit\Framework\Event\TraceableEventDispatcher;
use Pagekit\Framework\ServiceProviderInterface;

class EventServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['events'] = function() {
            return new EventDispatcher;
        };

        $app['profiler.events'] = $app->protect(function($dispatcher, $stopwatch) {
            return new TraceableEventDispatcher($dispatcher, $stopwatch);
        });
    }

    public function boot(Application $app)
    {
    }
}
