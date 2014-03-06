<?php

namespace Pagekit\Component\Cookie;

use Pagekit\Framework\Application;
use Pagekit\Framework\ServiceProviderInterface;

class CookieServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
		$app['cookie'] = function($app) {

            $app['cookie.init'] = true;

            $request = $app['request'];
            $path    = $app['config']->get('cookie.path', rtrim($request->getBaseUrl(), '/'));
            $domain  = $app['config']->get('cookie.domain');

			return new CookieJar($request, $path, $domain);
		};
	}

    public function boot(Application $app)
    {
        $app->after(function($request, $response) use ($app) {
			if (isset($app['cookie.init'])) {
                foreach ($app['cookie']->getQueuedCookies() as $cookie) {
                    $response->headers->setCookie($cookie);
                }
			}
		});
    }
}
