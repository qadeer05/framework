<?php

namespace Pagekit\Tests;

use Pagekit\Component\Config\Config;
use Pagekit\Framework\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;


class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Request
     */
    protected $request;

	public function setUp()
	{
		$this->request = $this->getMock('Symfony\Component\HttpFoundation\Request');
		$this->app = $this->createApplication();
	}

	public function createApplication()
	{
		$app = new Application;
		$app['session'] = new Session(new MockArraySessionStorage);
		$app['request'] = $this->request;

		return $app;
	}

	public function getConfig($settings)
	{
		$config = new Config;
		foreach ($settings as $key => $value) {
			$config->set($key, $value);
		}
		return $config;
	}
}