<?php

namespace Pagekit\Framework\Provider;

use Pagekit\Framework\Application;
use Pagekit\Framework\ServiceProviderInterface;
use Pagekit\Framework\Templating\Helper\GravatarHelper;
use Pagekit\Framework\Templating\Helper\TokenHelper;
use Pagekit\Framework\Templating\RazrEngine2;
use Pagekit\Razr\Directive\FunctionDirective;

class RazrServiceProvider2 implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['tmpl.razr2'] = function($app) {

            $parser = $app['tmpl.parser'];
            $parser->addEngine('razr2', '.razr');

            $engine = new RazrEngine2($parser, $app['path'].'/app/cache/templates');
            $engine->addDirective(new FunctionDirective('gravatar', array(new GravatarHelper, 'get')));
            $engine->addGlobal('app', $app);

            if (isset($app['view'])) {
                $engine->addDirective(new FunctionDirective('action', array($app['view'], 'callAction')));
            }

            if (isset($app['view.styles'])) {
                $engine->addDirective(new FunctionDirective('style', function($name, $asset = null, $dependencies = array(), $options = array()) use ($app) {
                    $app['view.styles']->queue($name, $asset, $dependencies, $options);
                }));
            }

            if (isset($app['view.scripts'])) {
                $engine->addDirective(new FunctionDirective('script', function($name, $asset = null, $dependencies = array(), $options = array()) use ($app) {
                    $app['view.scripts']->queue($name, $asset, $dependencies, $options);
                }));
            }

            if (isset($app['csrf'])) {
                $engine->addDirective(new FunctionDirective('token', array(new TokenHelper($app['csrf']), 'generate')));
            }

            if (isset($app['markdown'])) {
                $engine->addDirective(new FunctionDirective('markdown', array($app['markdown'], 'parse')));
            }

            if (isset($app['translator'])) {
                $engine->addDirective(new FunctionDirective('trans', array($app['translator'], 'trans')));
                $engine->addDirective(new FunctionDirective('transchoice', array($app['translator'], 'transChoice')));
                $engine->addFunction('trans', array($app['translator'], 'trans'));
                $engine->addFunction('transchoice', array($app['translator'], 'transChoice'));
            }

            return $engine;
        };
    }

    public function boot(Application $app)
    {
        $app['tmpl']->addEngine($app['tmpl.razr2']);
    }
}
