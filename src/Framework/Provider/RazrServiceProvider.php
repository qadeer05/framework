<?php

namespace Pagekit\Framework\Provider;

use Pagekit\Framework\Application;
use Pagekit\Framework\ServiceProviderInterface;
use Pagekit\Framework\Templating\Helper\GravatarHelper;
use Pagekit\Framework\Templating\Helper\TokenHelper;
use Pagekit\Framework\Templating\RazrEngine;
use Razr\Environment;
use Razr\Loader\FilesystemLoader;
use Razr\SimpleFunction;

class RazrServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['tmpl.razr'] = function($app) {

            $parser = $app['tmpl.parser'];
            $parser->addEngine('razr', '.razr.php');

            $env = new Environment(new FilesystemLoader, array('cache' => $app['path'].'/app/cache/templates'));
            $env->addFunction(new SimpleFunction('gravatar', array(new GravatarHelper, 'get')));

            if (isset($app['view'])) {
                $env->addFunction(new SimpleFunction('action', array($app['view'], 'callAction')));
            }

            if (isset($app['view.styles'])) {
                $env->addFunction(new SimpleFunction('style', function($name, $asset = null, $dependencies = array(), $options = array()) use ($app) {
                    $app['view.styles']->queue($name, $asset, $dependencies, $options);
                }));
            }

            if (isset($app['view.scripts'])) {
                $env->addFunction(new SimpleFunction('script', function($name, $asset = null, $dependencies = array(), $options = array()) use ($app) {
                    $app['view.scripts']->queue($name, $asset, $dependencies, $options);
                }));
            }

            if (isset($app['csrf'])) {
                $env->addFunction(new SimpleFunction('token', array(new TokenHelper($app['csrf']), 'generate')));
            }

            if (isset($app['markdown'])) {
                $env->addFunction(new SimpleFunction('markdown', array($app['markdown'], 'parse')));
            }

            if (isset($app['translator'])) {
                $env->addFunction(new SimpleFunction('trans', array($app['translator'], 'trans')));
                $env->addFunction(new SimpleFunction('transchoice', array($app['translator'], 'transChoice')));
            }

            return new RazrEngine($parser, $env);
        };
    }

    public function boot(Application $app)
    {
        $app['tmpl']->addEngine($app['tmpl.razr']);
    }
}
