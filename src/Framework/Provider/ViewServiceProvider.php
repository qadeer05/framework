<?php

namespace Pagekit\Framework\Provider;

use Pagekit\Component\View\Event\ActionEvent;
use Pagekit\Component\View\Templating\Helper\ActionHelper;
use Pagekit\Component\View\Templating\Helper\GravatarHelper;
use Pagekit\Component\View\Templating\Helper\MarkdownHelper;
use Pagekit\Component\View\Templating\Helper\ScriptHelper;
use Pagekit\Component\View\Templating\Helper\StyleHelper;
use Pagekit\Component\View\Templating\Helper\TokenHelper;
use Pagekit\Component\View\Templating\TemplateNameParser;
use Pagekit\Component\View\ViewServiceProvider as BaseViewServiceProvider;
use Pagekit\Framework\Application;
use Pagekit\Framework\Templating\RazrEngine;
use Razr\Environment;
use Razr\Loader\FilesystemLoader as RazrFilesystemLoader;
use Razr\SimpleFunction;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader as PhpFilesystemLoader;
use Symfony\Component\Templating\PhpEngine;

class ViewServiceProvider extends BaseViewServiceProvider
{
    public function register(Application $app)
    {
        parent::register($app);

        $app['view.parser'] = function($app) {
            return new TemplateNameParser($app['events']);
        };
    }

    public function boot(Application $app)
    {
        parent::boot($app);

        $this->registerPhpEngine($app);
        $this->registerRazrEngine($app);

        $app['view']->addAction('head', function(ActionEvent $event) use ($app) {
            $event->append(sprintf('<meta name="generator" content="Pagekit %1$s" data-version="%1$s" data-base="%2$s" />', $app['config']['app.version'], $app['url']->root()));
        }, 16);
    }

    public function registerPhpEngine(Application $app)
    {
        $engine = new PhpEngine($app['view.parser'], new PhpFilesystemLoader(array()));
        $engine->addHelpers(array(new SlotsHelper, new ActionHelper($app['view']), new StyleHelper($app['view.styles']), new ScriptHelper($app['view.scripts']), new TokenHelper($app['csrf']), new GravatarHelper, new MarkdownHelper($app['markdown'])));

        $app['view']->addEngine($app['view.engine.php'] = $engine);
        $app['view.parser']->addEngine('php', '.php');
    }

    public function registerRazrEngine(Application $app)
    {
        $engine = new RazrEngine($app['view.parser'], $env = new Environment(new RazrFilesystemLoader, array('cache' => $app['path'].'/app/cache/templates')));

        $env->addGlobal('url', $app['url']);
        $env->addFunction(new SimpleFunction('action', array($app['view'], 'callAction')));
        $env->addFunction(new SimpleFunction('style', array($app['view.styles'], 'queue')));
        $env->addFunction(new SimpleFunction('script', array($app['view.scripts'], 'queue')));
        $env->addFunction(new SimpleFunction('token', array(new TokenHelper($app['csrf']), 'generate')));
        $env->addFunction(new SimpleFunction('trans', array($app['translator'], 'trans')));
        $env->addFunction(new SimpleFunction('transchoice', array($app['translator'], 'transChoice')));
        $env->addFunction(new SimpleFunction('markdown', array($app['markdown'], 'parse')));
        $env->addFunction(new SimpleFunction('gravatar', array(new GravatarHelper, 'get')));

        $env->addFunction(new SimpleFunction('style', function($name, $asset = null, $dependencies = array(), $options = array()) use ($app) {
            $app['view.styles']->queue($name, $asset, $dependencies, $options);
        }));

        $env->addFunction(new SimpleFunction('script', function($name, $asset = null, $dependencies = array(), $options = array()) use ($app) {
            $app['view.scripts']->queue($name, $asset, $dependencies, $options);
        }));

        $app['view']->addEngine($app['view.engine.razr'] = $engine);
        $app['view.parser']->addEngine('razr', '.razr.php');
    }
}
