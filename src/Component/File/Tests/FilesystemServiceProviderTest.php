<?php

namespace Pagekit\Component\File\Tests;

use Pagekit\Component\File\FilesystemServiceProvider;
use Pagekit\Framework\Application;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * TODO: should probably extend ServiceProviderTest
 */
class FilesystemServiceProviderTest extends \PHPUnit_Framework_TestCase
{
	public function testFilesystemServiceProvider()
	{
		$app = $this->getApplication();
		$provider = new FilesystemServiceProvider;
		$provider->register($app);
		$provider->boot($app);

		$this->assertInstanceOf('Pagekit\Component\File\Filesystem', $app['files']);
	}

	protected function getApplication()
	{
		$this->request = $this->getMock('Symfony\Component\HttpFoundation\Request');
		$app = new Application;
		$app['session'] = new Session(new MockArraySessionStorage);
		$app['request'] = $this->request;
		$app->boot();

		return $app;
	}
}