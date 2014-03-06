<?php

namespace Pagekit\Framework\Provider;

use Pagekit\Framework\Application;
use Pagekit\Framework\Exception\ExceptionHandler;
use Pagekit\Framework\ServiceProviderInterface;
use Symfony\Component\Debug\ErrorHandler;

class ExceptionServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $debug   = isset($app['config']) ? $app['config']['app.debug'] : true;
        $error   = ErrorHandler::register(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
        $handler = ExceptionHandler::register($debug);

        if ($cli = $app->runningInConsole() or $debug) {
            ini_set('display_errors', 1);
        }

        $app['exception'] = $handler;
    }

    public function boot(Application $app)
    {
    }
}
