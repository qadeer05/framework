<?php

namespace Pagekit\Framework\Provider;

use Pagekit\Framework\Application;
use Pagekit\Framework\ServiceProviderInterface;

class ConsoleServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
    }

    public function boot(Application $app)
    {
        if ($app->runningInConsole()) {
        	$app->on('console.init', function($event) {

        		$console = $event->getConsole();
                $namespace = 'Pagekit\\Framework\\Console\\Command\\';

                foreach (glob(__DIR__.'/../Console/Command/*Command.php') as $file) {
                    $class = $namespace.basename($file, '.php');
                    $console->add(new $class);
                }

        	});
        }
    }
}
