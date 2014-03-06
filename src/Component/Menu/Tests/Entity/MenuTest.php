<?php

namespace Pagekit\Component\Menu\Entity\Tests;

class MenuTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->menu = $this->getMockForAbstractClass('Pagekit\Component\Menu\Entity\Menu');
	}

	public function testSetGetName()
	{
		$this->menu->setName('someName');
		$this->assertEquals('someName', $this->menu->getName());
	}
}