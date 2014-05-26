<?php

namespace Pagekit\Component\Cache\Tests;

use Pagekit\Component\Cache\CacheServiceProvider;
use Pagekit\Tests\ServiceProviderTest;

class CacheServiceProviderTest extends ServiceProviderTest
{
	public function setUp()
	{
		parent::setUp();
	}

	public function configProvider()
	{
		return array(
			array('array'),
			array('apc'),
			array('file'),
			array('auto'));
	}

	/**
	* @dataProvider configProvider
	*/
	public function testRegister($storage)
	{
		$provider = new CacheServiceProvider;
		$config   = array('cache.cache.test' => array('storage' => $storage, 'path' => './', 'cache.prefix' => 'prefix_'));

		$this->app['config'] = $this->getConfig($config);
		$this->app->boot();

		if ($storage == 'apc') {
			if (!extension_loaded('apc') || false === @apc_cache_info()) {
				$this->markTestSkipped('The ' . __CLASS__ .' requires the use of APC');
			} else {
				$provider->register($this->app);
				$this->assertInstanceOf('Pagekit\Component\Cache\Cache', $this->app['cache']);
			}
		}

		if ($storage ==  'array') {
			$provider->register($this->app);
			$this->assertInstanceOf('Pagekit\Component\Cache\Cache', $this->app['cache']);
		}

		if ($storage ==  'file' || $storage == 'auto') {
			$provider->register($this->app);
			$this->assertInstanceOf('Pagekit\Component\Cache\Cache', $this->app['cache']);
		}
	}
}
