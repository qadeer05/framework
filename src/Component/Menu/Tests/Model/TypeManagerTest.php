<?php

namespace Pagekit\Component\Menu\Model\Tests;

use Pagekit\Component\Menu\Model\TypeManager;

class TypeManagerTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->typeManager = new TypeManager;
		$this->type = $this->getMock('Pagekit\Component\Menu\Model\TypeInterface');
	}

	public function testRegisterUnregister()
	{
		$this->type->expects($this->any())
				->method('getName')
				->will($this->returnValue('typeName'));

		$this->typeManager->register($this->type);
		$this->assertInstanceOf('Pagekit\Component\Menu\Model\TypeInterface', $this->typeManager->get('typeName'));
		$this->assertEquals('typeName', $this->typeManager->get('typeName')->getName());

		$this->typeManager->unregister('typeName');
		$this->assertEquals(null, $this->typeManager->get('typeName'));
	}

	/**
	* @expectedException	RuntimeException
	* @exceptionMessage		Class something does not exist and could not be loaded
	*/
	public function testRegisterException()
	{
		$this->typeManager->register('Pagekit\Component\Menu\Model\Menu');
	}

	public function testGetIterator()
	{
		$this->assertInstanceOf('\ArrayIterator', $this->typeManager->getIterator());
	}
}