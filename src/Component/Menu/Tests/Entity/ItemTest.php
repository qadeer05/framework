<?php

namespace Pagekit\Component\Menu\Entity\Tests;

class ItemTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->item = $this->getMockForAbstractClass('Pagekit\Component\Menu\Entity\Item');
	}

	public function testSetGetName()
	{
		$this->item->setName('someName');
		$this->assertEquals('someName', $this->item->getName());
	}

	public function testSetGetMenuId()
	{
		$this->item->setMenuId('someMenuId');
		$this->assertEquals('someMenuId', $this->item->getMenuId());
	}

	public function testSetGetPriority()
	{
		$this->item->setPriority(23);
		$this->assertEquals(23, $this->item->getPriority());
	}

	public function testSetGetData()
	{
		$data = array('foo' => 'bar');
		$this->item->setData($data);
		$this->assertEquals($data, $this->item->getData());
	}
}