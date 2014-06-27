<?php

namespace Pagekit\Component\Routing;

use Pagekit\Component\Filter\FilterManager;
use Pagekit\Component\Routing\Controller\ControllerReader;
use Pagekit\Component\Routing\EventListener\LoaderListener;
use Pagekit\Component\Routing\EventListener\StringToResponseListener;
use Pagekit\Component\Routing\Loader\RouteLoader;
use Pagekit\Component\Routing\Request\EventListener\ParamFetcherListener;
use Pagekit\Component\Routing\Request\ParamFetcher;
use Pagekit\Component\Routing\Request\ParamReader;
use Pagekit\Framework\Application;
use Pagekit\Framework\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;

class RoutingServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['router'] = function($app) {

            $reader = new ControllerReader($app['events']);
            $loader = new RouteLoader($reader);

            return new Router($app['kernel'], $loader, $app['resolver'], isset($app['config']) ? $app['config']['app.debug'] : true);
        };

        $app['kernel'] = function($app) {
            return new HttpKernel($app['events'], $app['resolver'], $app['request_stack']);
        };

        $app['request_stack'] = function () {
            return new RequestStack;
        };

        $app['response'] = function($app) {
            return new Response($app['url']);
        };

        $app['resolver'] = function() {
            return new ControllerResolver;
        };

        $app['url'] = function($app) {
            return new UrlProvider($app['router'], $app['locator']);
        };
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        $app['events']->addSubscriber(new ParamFetcherListener(new ParamReader, new ParamFetcher(new FilterManager)));
        $app['events']->addSubscriber(new RouterListener($app['router'], null, null, $app['request_stack']));
        $app['events']->addSubscriber(new LoaderListener($app['router']));
        $app['events']->addSubscriber(new ResponseListener('UTF-8'));
        $app['events']->addSubscriber(new StringToResponseListener);
    }
}
