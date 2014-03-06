<?php

namespace Pagekit\Component\File\Tests;

use Pagekit\Tests\FileUtil;

/**
 * Todo: needs to be completed
 */
class ReadOnlyStreamWrapperTest extends \PHPUnit_Framework_TestCase
{
    use FileUtil;

    private $workspace;

    public function setUp()
    {
        $this->workspace = $this->getTempDir('file_wrapper_');

        file_put_contents($this->workspace.'/testfile', 'foo');

        stream_wrapper_register('foo', 'Pagekit\Component\File\ReadOnlyStreamWrapper');
    }

    public function tearDown()
    {
        stream_wrapper_unregister('foo');
        $this->removeDir($this->workspace);
    }

    public function testFopen()
    {
        $handle = fopen('foo://'.$this->workspace.'/testfile', 'r');

        $this->assertTrue(is_resource($handle));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testFopenFail()
    {
        fopen('foo://'.$this->workspace.'/testfile', 'w');
    }

    public function testFclose()
    {
        $handle = fopen('foo://'.$this->workspace.'/testfile', 'r');
        $this->assertTrue(fclose($handle));
    }

    public function testFlock()
    {
        $handle = fopen('foo://'.$this->workspace.'/testfile', 'r');
        $this->assertTrue(flock($handle, LOCK_SH));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testFlockFail()
    {
        $handle = fopen('foo://'.$this->workspace.'/testfile', 'r');
        $this->assertTrue(flock($handle, LOCK_EX));
    }
}