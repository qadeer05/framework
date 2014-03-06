<?php

namespace Pagekit\Component\Migration;

use Pagekit\Framework\Application;
use Pagekit\Framework\ServiceProviderInterface;

class MigrationServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['migrator'] = function() {
            return new Migrator;
        };
    }

    public function boot(Application $app)
    {
    }
}
