<?php

namespace Pagekit\Framework\Provider;

use Pagekit\Component\Package\Installer\PackageInstaller;
use Pagekit\Framework\Application;
use Pagekit\Framework\Cache\FileCache;
use Pagekit\Framework\Extension\Event\LoadFailureEvent;
use Pagekit\Framework\Extension\Exception\ExtensionLoadException;
use Pagekit\Framework\Extension\ExtensionManager;
use Pagekit\Framework\Package\Loader\ExtensionLoader;
use Pagekit\Framework\Package\Repository\ExtensionRepository;
use Pagekit\Framework\ServiceProviderInterface;

class ExtensionServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['extensions'] = function($app) {

            $loader     = new ExtensionLoader;
            $repository = new ExtensionRepository($app['config']['extension.path'], $loader);
            $installer  = new PackageInstaller($repository, $loader);

            return new ExtensionManager($app, $repository, $installer, $app['autoloader'], $app['locator']);
        };

        $app['extensions.boot'] = array();
    }

    public function boot(Application $app)
    {
        foreach (array_unique($app['extensions.boot']) as $extension) {
            try {
                $app['extensions']->load($extension)->boot($app);
            } catch (ExtensionLoadException $e) {
                $app['events']->dispatch('extension.load_failure', new LoadFailureEvent($extension));
            }
        }
    }
}
