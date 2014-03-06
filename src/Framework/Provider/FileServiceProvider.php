<?php

namespace Pagekit\Framework\Provider;

use Pagekit\Framework\Application;
use Pagekit\Framework\File\FileProvider;
use Pagekit\Framework\ServiceProviderInterface;

class FileServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['file'] = function($app) {
            return new FileProvider($app);
        };
    }

    public function boot(Application $app)
    {
    }
}
