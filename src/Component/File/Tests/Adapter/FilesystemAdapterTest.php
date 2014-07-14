<?php

namespace Pagekit\Component\File\Tests\Adapter;

use Pagekit\Component\File\Adapter\FilesystemAdapter;
use Pagekit\Tests\FileUtil;

/**
 * Test class for FilesystemAdapter.
 */
class FilesystemAdapterTest extends \PHPUnit_Framework_TestCase
{
    use FileUtil;

    /**
     * @var string
     */
    protected $workspace;

    /**
     * @var FilesystemAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $this->workspace = $this->getTempDir('file_filesystemadapter_');
        $this->adapter = new FilesystemAdapter($this->workspace);
    }

    public function tearDown()
    {
        $this->removeDir($this->workspace);
    }

    public function testFileSize()
    {
        $sizeFilePath = $this->workspace.'/size_source_file';

        file_put_contents($sizeFilePath, 'foo');
        $this->assertEquals(3, $this->adapter->size($sizeFilePath));

        // Test exception
        $this->assertFalse($this->adapter->size($this->workspace.'/doesnotexist'));
    }

    public function testFileModified()
    {
        $modifiedFilePath = $this->workspace.'/modified_source_file';

        touch($modifiedFilePath);
        $this->assertEquals(filemtime($modifiedFilePath), $this->adapter->mtime($modifiedFilePath));

        // Test exception
        $this->assertFalse($this->adapter->mtime($this->workspace.'/doesnotexist'));
    }

    public function testAppend()
    {
        $appendFilePath = $this->workspace.'/append_source_file';

        $this->adapter->write($appendFilePath, 'foo', true);

        $this->assertFileExists($appendFilePath);
        $this->assertEquals('foo', file_get_contents($appendFilePath));

        $this->adapter->write($appendFilePath, 'bar', true);
        $this->assertEquals('foobar', file_get_contents($appendFilePath));
    }


    public function testAppendFail()
    {
        try {
            $this->adapter->write($this->workspace, 'can not append to directories', true);
        } catch (\Exception $e) {
            $this->assertEquals('file_put_contents('.$this->workspace.'): failed to open stream: Is a directory', $e->getMessage());
        }
    }

    public function testRead()
    {
        $contentFilePath = $this->workspace.'/content_source_file';

        file_put_contents($contentFilePath, 'foo');
        $this->assertFileExists($contentFilePath);
        $this->assertEquals('foo', $this->adapter->read($contentFilePath));

        // Test exception
        $this->assertFalse($this->adapter->read($contentFilePath.'doesnotexist'));
    }

    public function testListDirectory()
    {
        touch($this->workspace.'/source_file_1');
        touch($this->workspace.'/source_file_2');
        touch($this->workspace.'/source_file_3');

        mkdir($this->workspace.'/source_dir_1');
        mkdir($this->workspace.'/source_dir_2');

        $result = $this->adapter->lsdir($this->workspace);
        $this->assertCount(3, $result['files']);
        $this->assertCount(2, $result['dirs']);
    }

    public function testIsDir()
    {
        $this->assertTrue($this->adapter->isDir($this->workspace));
    }

    public function testMkdir()
    {
        $directory = $this->workspace.'/folder1/folder2';
        $this->adapter->mkdir($directory, 0755, true);

        $this->assertTrue(is_dir($directory));
    }

    public function testMkdirFailed()
    {
        try {
            $this->adapter->mkdir($this->workspace.'/folder1/folder2', 0755, false);
        } catch (\Exception $e) {
            $this->assertEquals('mkdir(): No such file or directory', $e->getMessage());
        }
    }

    public function testEnsureDirExists()
    {
        $dir = $this->workspace.'/folder1';
        $this->assertFalse(is_dir($dir));
        $this->adapter->ensuredir($dir);
        $this->assertTrue(is_dir($dir));

        $file = $this->workspace.'/file';
        touch($file);
        try {
            $this->assertFalse($this->adapter->ensuredir($file));
        } catch (\Exception $e) {
            $this->assertEquals('mkdir(): File exists', $e->getMessage());
        }
    }

    public function testRmdir()
    {

        $directory = $this->workspace.'/testRmdir1/testRmdir2';
        mkdir($directory, 0755, true);

        $this->assertTrue(is_dir($directory));

        $this->adapter->rmdir($directory);

        $this->assertFalse(is_dir($directory));
    }

    public function testRmdirFailed()
    {
        $this->assertFalse($this->adapter->rmdir('folder1/folder2unknown'));
    }

    public function testUnlink()
    {
        $file = $this->workspace.'/source_file_1';
        touch($file);
        $this->assertTrue(is_file($file));
        $this->adapter->unlink($file);
        $this->assertFalse(is_file($file));
    }

    public function testUnlinkFailed()
    {
        $this->assertFalse($this->adapter->unlink('folder1/fileunknown'));
    }

    public function testRename()
    {
        $file = $this->workspace.'/source_file_1';
        $file2 = $this->workspace.'/source_file_2';
        touch($file);
        $this->assertTrue(is_file($file));

        $this->adapter->rename($file, $file2);

        $this->assertTrue(is_file($file2));
        $this->assertFalse(is_file($file));
    }

    public function testRenameFailed()
    {
        try {
            $this->adapter->rename('folder1/fileunknown', 'folder1/fileunknown2');
        } catch (\Exception $e) {
            $this->assertEquals('rename(folder1/fileunknown,folder1/fileunknown2): No such file or directory', $e->getMessage());
        }
    }

    public function testCopy()
    {
        $file = $this->workspace.'/source_file_1';
        $file2 = $this->workspace.'/source_file_2';
        touch($file);
        $this->assertTrue(is_file($file));
        $this->assertFalse(is_file($file2));

        $this->adapter->copy($file, $file2);

        $this->assertTrue(is_file($file));
        $this->assertTrue(is_file($file2));
    }

    public function testCopyFailed()
    {
        try {
            $this->adapter->copy('folder1/fileunknown', 'folder1/fileunknown2');
        } catch (\Exception $e) {
            $this->assertEquals('copy(folder1/fileunknown): failed to open stream: No such file or directory', $e->getMessage());
        }
    }

    public function testExists()
    {
        $file = $this->workspace.'/source_file_1';
        $file2 = $this->workspace.'/source_file_2';
        touch($file);
        $this->assertTrue($this->adapter->exists($file));
        $this->assertFalse($this->adapter->exists($file2));
    }

    /**
     * @dataProvider providePathsForNormalizePath
     */
    public function testNormalizePath($path, $expectedResult)
    {
        $result = $this->adapter->normalizePath($path);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function providePathsForNormalizePath()
    {
        return [
            ['/', '/'],
            ['/../', '/'],
            ['/var/lib', '/var/lib'],
            ['c:\\\\var\\lib', 'c:/var/lib'],
            ['c:\\..\\var\\lib', 'c:/var/lib'],
            ['c:\\\\var\\lib\\..', 'c:/var'],
            ['c:\\..\\var\\..\\lib', 'c:/lib'],
            ['\\var\\lib', '/var/lib'],
            ['var/lib', 'var/lib'],
            ['../var/lib', 'var/lib'],
            ['', ''],
            [null, '']
        ];
    }

    /**
     * @return array
     */
    public function providePathsForIsAbsolutePath()
    {
        return [
            ['/var/lib', true],
            ['c:\\\\var\\lib', true],
            ['\\var\\lib', true],
            ['var/lib', false],
            ['../var/lib', false],
            ['', false],
            [null, false]
        ];
    }
}