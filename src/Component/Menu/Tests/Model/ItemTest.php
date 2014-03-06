<?php

namespace Pagekit\Component\Menu\Tests\Model;

use Pagekit\Component\Menu\Model\Item;

class ItemTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->item = new Item;
	}

	public function testSetGetId()
	{
		$this->item->setId('someId');
		$this->assertEquals('someId', $this->item->getId());
	}

	public function testSetGetName()
	{
		$this->item->setName('someName');
		$this->assertEquals('someName', $this->item->getName());
	}

	public function testSetGetUrl()
	{
		$this->item->setUrl('someUrl');
		$this->assertEquals('someUrl', $this->item->getUrl());
	}

	public function testSetGetAttributes()
	{
		$this->item->setAttributes(array('key' => 'value'));
		$this->assertArrayHasKey('key', $this->item->getAttributes());
	}

	public function testSetGetAttribute()
	{
		$this->item->setAttributes(array('key' => ''));
		$this->assertEquals('', $this->item->getAttribute('key'));
		$this->item->setAttribute('key', 'value');
		$this->assertEquals('value', $this->item->getAttribute('key'));
	}

	public function testSetGetParentId()
	{
		$this->item->setParentId(1);
		$this->assertEquals(1, $this->item->getParentId());
	}

	public function testSetGetMenu()
	{
		$menu = $this->getMock('Pagekit\Component\Menu\Model\MenuInterface');
		$this->item->setMenu($menu);
		$this->assertInstanceOf('Pagekit\Component\Menu\Model\MenuInterface', $this->item->getMenu());
	}

	public function testSetGet()
	{
		$this->assertEquals('', $this->item->get('Name'));
		$this->item->set('Name', 'someName');
		$this->assertEquals('someName', $this->item->get('Name'));

		$this->assertEquals('', $this->item->get('aProperty'));
		$this->item->set('aProperty', 'PropertyValue');
		$this->assertEquals('PropertyValue', $this->item->get('aProperty'));
	}

	/**
	* @expectedException \InvalidArgumentException
	*/
	public function testSetException()
	{
		$this->item->set('', '');
	}

	/**
	* @expectedException \InvalidArgumentException
	*/
	public function testGetException()
	{
		$this->item->get('', '');
	}

	public function testHashCode()
	{
		$this->item->setId('someHashCode');
		$this->assertEquals('someHashCode', $this->item->hashCode());
	}

	public function testToString()
	{
		$this->item->setName('someHash');
		$this->assertEquals('someHash', $this->item->__toSTring());
	}
}