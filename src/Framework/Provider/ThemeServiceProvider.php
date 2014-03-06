<?php

namespace Pagekit\Framework\Provider;

use Pagekit\Component\Package\Installer\PackageInstaller;
use Pagekit\Framework\Application;
use Pagekit\Framework\Package\Loader\ThemeLoader;
use Pagekit\Framework\Package\Repository\ThemeRepository;
use Pagekit\Framework\ServiceProviderInterface;
use Pagekit\Framework\Theme\ThemeManager;

class ThemeServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['themes'] = function($app) {

            $loader     = new ThemeLoader;
            $repository = new ThemeRepository($app['config']['theme.path'], $loader);
            $installer  = new PackageInstaller($repository, $loader);

            return new ThemeManager($app, $repository, $installer, $app['autoloader'], $app['locator']);
        };
    }

    public function boot(Application $app)
    {
    }
}
