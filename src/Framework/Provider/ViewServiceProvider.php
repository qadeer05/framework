<?php

namespace Pagekit\Framework\Provider;

use Pagekit\Component\View\View;
use Pagekit\Component\View\Event\ActionEvent;
use Pagekit\Component\View\ViewServiceProvider as BaseViewServiceProvider;
use Pagekit\Framework\Application;

class ViewServiceProvider extends BaseViewServiceProvider
{
    public function register(Application $app)
    {
        $app['view'] = function($app) {

            $view = new View($app['events'], $app['tmpl']);
            $view->set('app', $app);

            return $view;
        };
    }

    public function boot(Application $app)
    {
        parent::boot($app);

        $app['view']->set('url', $app['url']);
        $app['view']->addAction('head', function(ActionEvent $event) use ($app) {
            $event->append(sprintf('<meta name="generator" content="Pagekit %1$s" data-version="%1$s" data-base="%2$s" />', $app['config']['app.version'], $app['url']->base() ?: '/'));
        }, 16);
    }
}
