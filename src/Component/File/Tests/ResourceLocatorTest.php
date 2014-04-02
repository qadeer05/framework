<?php

namespace Pagekit\Component\File\Tests;

use Pagekit\Component\File\ResourceLocator;

/**
 * TODO: should probably extend ServiceProviderTest
 */
class ResourceLocatorTest extends \PHPUnit_Framework_TestCase
{
	use \Pagekit\Tests\FileUtil;

	/**
	 * @var string
	 */
	protected $workspace;

	/**
	 * @var Filesystem
	 */
	protected $locator;

	public function setUp()
	{
	    $this->workspace = $this->getTempDir('file_filesystem_');
	    $this->locator = new ResourceLocator;
	}

	public function testLocator()
	{
		$this->locator->addPath('test', 'foo/bar', array($this->workspace.'/path/to/foo/bar/1', $this->workspace.'/path/to/foo/bar/2/'));
		$this->locator->addPath('test', 'bar', $this->workspace.'/path/to/bar');
		$this->locator->addPath('test2', 'foo/bar', array($this->workspace.'/path/to/foo/bar/1', $this->workspace.'/path/to/foo/bar/3'));

		mkdir($this->workspace.'/path/to/foo/bar/1' , 0775, true);
		mkdir($this->workspace.'/path/to/foo/bar/2' , 0775, true);
		mkdir($this->workspace.'/path/to/bar/2' , 0775, true);

		foreach ($this->locator->findResourceVariants('test://foo/bar') as $path) {
			touch($path.'/1.txt');
		}
		touch($this->locator->findResource('test://bar').'/2/1.txt');

		$this->assertEquals($this->workspace.'/path/to/foo/bar/1/1.txt', $this->locator->findResource('test://foo/bar/1.txt'));
		$this->assertEquals(array($this->workspace.'/path/to/foo/bar/1/1.txt', $this->workspace.'/path/to/foo/bar/2/1.txt'), $this->locator->findResourceVariants('test://foo/bar/1.txt'));
		$this->assertFalse($this->locator->findResource('test://foo/bar/2.txt'));
		$this->assertEquals(array($this->workspace.'/path/to/foo/bar/1/1.txt'), $this->locator->findResourceVariants('test2://foo/bar/1.txt'));
	}

    /**
     * @expectedException InvalidArgumentException
     */
	public function testException1()
	{
		$this->locator->findResource('foo');
	}

	/**
     * @expectedException InvalidArgumentException
     */
	public function testException2()
	{
		$this->locator->findResource('foo://');
	}
}