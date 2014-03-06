<?php

namespace Pagekit\Component\Menu\Tests\Model;

use Pagekit\Component\Menu\Model\Menu;

class MenuTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->menu = new Menu;
	}

	public function testSetGetId()
	{
		$this->assertEquals('', $this->menu->getId());
		$this->menu->setId('someID');
		$this->assertEquals('someID', $this->menu->getId());
	}

	public function testAddGetItem()
	{
		$this->assertEquals(null, $this->menu->getItem('someItem'));

		$item = $this->getMock('Pagekit\Component\Menu\Model\ItemInterface');
		$item->expects($this->any())
			->method('getId')
			->will($this->returnValue('someID'));
		$this->menu->addItem($item);
		$this->assertInstanceOf('Pagekit\Component\Menu\Model\ItemInterface', $this->menu->getItem('someID'));
	}

	public function testSetGetItems()
	{
		$this->assertEquals(0, count($this->menu->getItems()));
		$this->menu->setItems(array(1, 2, 3));
		$this->assertEquals(array(1, 2, 3), $this->menu->getItems());
	}

	public function testGetIterator()
	{
		$this->assertInstanceOf('ArrayIterator', $this->menu->getIterator());
	}
}