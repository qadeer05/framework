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
	public function testRegister($config)
	{
		$cacheConfig = array('cache.storage' => $config, 'cache.path' => './', 'cache.prefix' => 'prefix_');
		$this->app->set('config', $this->getConfig($cacheConfig));
		$this->app->boot();

		$provider = new CacheServiceProvider;
		
		if ($config == 'apc') {
			if ( ! extension_loaded('apc') || false === @apc_cache_info()) {
				$this->markTestSkipped('The ' . __CLASS__ .' requires the use of APC');
			}
			else {
				$provider->register($this->app);
				$this->assertInstanceOf('Pagekit\Component\Cache\Cache', $this->app['cache']);
			}
		}
		if ($config ==  'array') {
			$provider->register($this->app);
			$this->assertInstanceOf('Pagekit\Component\Cache\Cache', $this->app['cache']);
		}
		if ($config ==  'file' || $config == 'auto') {
			$provider->register($this->app);
			$this->assertInstanceOf('Pagekit\Component\Cache\Cache', $this->app['cache']);
		}
	}
}