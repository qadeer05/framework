<?php

namespace Pagekit\Component\Auth\Tests;

use Pagekit\Component\Auth\Auth;
use Pagekit\Component\Auth\AuthServiceProvider;
use Pagekit\Tests\ServiceProviderTest;


class AuthServiceProviderTest extends ServiceProviderTest
{
	public function setUp()
	{
		parent::setUp();
		$this->user = $this->getMockBuilder('Pagekit\Component\Auth\UserInterface')->disableOriginalConstructor()->getMock();
		$this->provider = new AuthServiceProvider;
		$this->app->boot();
	}

	public function testRegister()
	{
		$this->provider->register($this->app);

		$this->assertInstanceOf('Pagekit\Component\Auth\Auth', $this->app['auth']);
		$this->assertInstanceOf('Pagekit\Component\Auth\Encoder\NativePasswordEncoder', $this->app['auth.password']);
	}

	public function testLogin()
	{
		$this->request->expects($this->once())
					  ->method('get')
					  ->with(Auth::REDIRECT_PARAM)
					  ->will($this->returnValue('/'));

		$this->provider->register($this->app);
		$this->provider->boot($this->app);

		$result = $this->app['auth']->login($this->user);

		$this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $result);
	}

	public function testLogout()
	{
		$this->request->expects($this->once())
					  ->method('get')
					  ->with(Auth::REDIRECT_PARAM)
					  ->will($this->returnValue('/'));

		$this->provider->register($this->app);
		$this->provider->boot($this->app);

		$result = $this->app['auth']->logout($this->user);

		$this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $result);
	}

	public function testGetSubscribedEvents()
	{
		$expected = [
					'auth.login'  => ['onLogin', -32],
					'auth.logout' => ['onLogout', -32],
					];
		$this->assertEquals($expected, $this->provider->getSubscribedEvents());
	}
}