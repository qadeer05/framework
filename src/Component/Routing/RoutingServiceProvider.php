<?php

namespace Pagekit\Component\Routing;

use Pagekit\Component\Filter\FilterManager;
use Pagekit\Component\Routing\Controller\ControllerReader;
use Pagekit\Component\Routing\EventListener\StringToResponseListener;
use Pagekit\Component\Routing\EventListener\UrlAliasListener;
use Pagekit\Component\Routing\Loader\CachedLoader;
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

            if (isset($app['cache'])) {
                $loader = new CachedLoader($loader, $app['cache'], $app['config']['app.debug']);
            }

            return new Router($app['events'], $app['kernel'], $loader);
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
            return new UrlGenerator($app['router'], $app['locator'], $app['events']);
        };
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        $app['events']->addSubscriber(new UrlAliasListener($app['router']->getUrlAliases()));
        $app['events']->addSubscriber(new ParamFetcherListener(new ParamReader, new ParamFetcher(new FilterManager)));
        $app['events']->addSubscriber(new RouterListener($app['router']->getUrlMatcher(), null, null, $app['request_stack']));
        $app['events']->addSubscriber(new ResponseListener('UTF-8'));
        $app['events']->addSubscriber(new StringToResponseListener);
    }
}
