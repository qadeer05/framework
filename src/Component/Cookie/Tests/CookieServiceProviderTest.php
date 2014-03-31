<?php

namespace Pagekit\Component\Cookie\Tests;

use Pagekit\Component\Config\Config;
use Pagekit\Component\Cookie\CookieServiceProvider;
use Pagekit\Framework\Application;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class CookieServiceProviderTest extends \PHPUnit_Framework_TestCase
{
	public function testCookieServiceProvider()
	{
		$app = $this->getApplication($this->getCookieConfig());
		$provider = new CookieServiceProvider;
		$provider->register($app);
		$provider->boot($app);

		$this->assertInstanceOf('Pagekit\Component\Cookie\CookieJar', $app['cookie']);
	}

	protected function getApplication($config)
	{
		$this->request = $this->getMock('Symfony\Component\HttpFoundation\Request');
		$app = new Application;
		$app['session'] = new Session(new MockArraySessionStorage);
		$app['request'] = $this->request;
		$app['config'] = $config;
		$app->boot();

		return $app;
	}

	protected function getCookieConfig()
	{
		$config = new Config;
		$config->set('cookie.path', 'path/to/cookie');
		$config->set('cookie.domain', 'localhost');
		return $config;
	}
}