<?php

namespace Pagekit\Component\File\Tests;

use Pagekit\Component\File\Adapter\FtpAdapter;
use Pagekit\Component\File\Exception\IOException;

/**
 * Test class for FilesystemAdapter.
 */
class FtpAdapterTest extends \Pagekit\Tests\FtpTestCase
{
    /**
     * @var FtpAdapter
     */
    protected $adapter;

    public function setUp()
    {
        parent::setUp();

        $this->adapter = new FtpAdapter($this->workspace, $GLOBALS['ftp_host'], $GLOBALS['ftp_user'], $GLOBALS['ftp_pass'], $GLOBALS['ftp_port'], $GLOBALS['ftp_passive'], true, $this->mode);
    }

    public function testFileSize()
    {
        $sizeFilePath = $this->workspace.'/size_source_file';

        $path = tempnam(sys_get_temp_dir(), 'ftp');
        file_put_contents($path, 'foo');

        ftp_put($this->connection, $sizeFilePath, $path, $this->mode);

        $this->assertEquals(3, $this->adapter->size($sizeFilePath));
    }

    /**
     * @expectedException \Pagekit\Component\File\Exception\IOException
     */
    public function testFileSizeFailes()
    {
        $sizeFilePath = $this->workspace.'/size_source_file_does_not_exist';
        $this->assertEquals(3, $this->adapter->size($sizeFilePath));
    }

    public function testFileModified()
    {
        $modifiedFilePath = $this->workspace.'/modified_source_file';

        $path = tempnam(sys_get_temp_dir(), 'ftp');

        ftp_put($this->connection, $modifiedFilePath, $path, $this->mode);

        $this->assertEquals(ftp_mdtm($this->connection, $modifiedFilePath), $this->adapter->mtime($modifiedFilePath));

        $this->setExpectedException('\Pagekit\Component\File\Exception\IOException');

        $this->adapter->mtime($this->workspace.'/unknown_file');
    }

    public function testAppend()
    {
        $appendFilePath = $this->workspace.DIRECTORY_SEPARATOR.'append_source_file';

        $this->adapter->write($appendFilePath, 'foo', true);

        $temp = fopen('php://temp', 'r+');
        ftp_fget($this->connection, $temp, $appendFilePath, $this->mode);
        rewind($temp);
        $this->assertEquals('foo', stream_get_contents($temp));

        $this->adapter->write($appendFilePath, 'bar', true);

        rewind($temp);
        ftp_fget($this->connection, $temp, $appendFilePath, $this->mode);
        rewind($temp);
        $this->assertEquals('foobar', stream_get_contents($temp));
        fclose($temp);

    }

    /**
     * @expectedException \Pagekit\Component\File\Exception\IOException
     */
    public function testAppendFail()
    {
        // Test exception
        $this->adapter->write($this->workspace, 'can not append to directories', true);
    }

    /**
     * @expectedException \Pagekit\Component\File\Exception\IOException
     */
    public function testRead()
    {
        $contentFilePath = $this->workspace.DIRECTORY_SEPARATOR.'content_source_file';

        $temp = fopen('php://temp', 'r+');
        fwrite($temp, 'foo');
        rewind($temp);
        ftp_fput($this->connection, $contentFilePath, $temp, $this->mode);
        fclose($temp);

        $this->assertEquals('foo', $this->adapter->read($contentFilePath));

        // Test exception
        $this->adapter->read($contentFilePath.'doesnotexist');

    }

    public function testListDirectory()
    {
        $temp = fopen('php://temp', 'r+');
        ftp_fput($this->connection, $this->workspace.DIRECTORY_SEPARATOR.'source_file_1', $temp, $this->mode);
        ftp_fput($this->connection, $this->workspace.DIRECTORY_SEPARATOR.'source_file_2', $temp, $this->mode);
        ftp_fput($this->connection, $this->workspace.DIRECTORY_SEPARATOR.'source_file_3', $temp, $this->mode);
        fclose($temp);

        ftp_mkdir($this->connection, $this->workspace.DIRECTORY_SEPARATOR.'source_dir_1');
        ftp_mkdir($this->connection, $this->workspace.DIRECTORY_SEPARATOR.'source_dir_2');

        $result = $this->adapter->listDirectory($this->workspace);
        $this->assertCount(3, $result['files']);
        $this->assertCount(2, $result['dirs']);
    }

    public function testIsDir()
    {
        $dir = $this->workspace.'/dir_1';
        $this->assertFalse($this->adapter->isDir($dir));
        ftp_mkdir($this->connection, $dir);
        $this->assertTrue($this->adapter->isDir($dir));
        $this->assertTrue($this->adapter->isDir('/'));
    }

    public function testMkdir()
    {
        $directory = 'testMkdir1/testMkdir2';
        
        $this->adapter->mkdir($directory, 0755, true);

        $this->assertTrue(ftp_chdir($this->connection, $this->workspace.'/'.$directory));
    }

    /**
     * @expectedException \Pagekit\Component\File\Exception\IOException
     */
    public function testMkdirFailedFileExists()
    {
        $temp = fopen('php://temp', 'r+');
        ftp_fput($this->connection, $this->workspace.'/source_file_1', $temp, $this->mode);
        fclose($temp);
        $this->adapter->mkdir($this->workspace.'/source_file_1');
    }

    /**
     * @expectedException \Pagekit\Component\File\Exception\IOException
     */
    public function testMkdirFailed()
    {
        $this->adapter->mkdir('testMkdirFailed1/testMkdirFailed2', 0755, false);
    }

    public function testEnsureDirExists()
    {
        $dir = $this->workspace.'/folder1';
        $this->adapter->ensureDirExists($dir);
        $this->assertTrue($this->adapter->isDir($dir));

        $this->setExpectedException('\Pagekit\Component\File\Exception\IOException');
        $temp = fopen('php://temp', 'r+');
        ftp_fput($this->connection, $this->workspace.'/source_file_1', $temp, $this->mode);
        fclose($temp);
        $this->adapter->ensureDirExists($this->workspace.'/source_file_1');
    }

    public function testRmdir()
    {
        ftp_mkdir($this->connection, $this->workspace.'/testRmdir1');
        $directory = $this->workspace.'/testRmdir1/testRmdir2';
        ftp_mkdir($this->connection, $directory);

        $this->assertTrue(@ftp_chdir($this->connection, $directory));

        $this->adapter->rmdir($directory);

        $this->assertFalse(@ftp_chdir($this->connection, $directory));
    }

    /**
     * @expectedException \Pagekit\Component\File\Exception\IOException
     */
    public function testRmdirFailed()
    {
        $this->adapter->rmdir('folder1/folder2unknown');
    }

    public function testUnlink()
    {
        $temp = fopen('php://temp', 'r+');
        ftp_fput($this->connection, $this->workspace.DIRECTORY_SEPARATOR.'source_file_1', $temp, $this->mode);
        fclose($temp);

        $this->assertTrue(ftp_size($this->connection, $this->workspace.DIRECTORY_SEPARATOR.'source_file_1') != -1);

        $this->adapter->unlink('source_file_1');

        $this->assertFalse(ftp_size($this->connection, $this->workspace.DIRECTORY_SEPARATOR.'source_file_1') != -1);
    }

    /**
     * @expectedException \Pagekit\Component\File\Exception\IOException
     */
    public function testUnlinkFailed()
    {
        $this->adapter->unlink('folder1/fileunknown');
    }

    public function testRename()
    {
        $temp = fopen('php://temp', 'r+');
        ftp_fput($this->connection, $this->workspace.'/source_file_1', $temp, $this->mode);
        fclose($temp);

        $this->assertTrue(ftp_size($this->connection, $this->workspace.'/source_file_1') != -1);

        $this->adapter->rename('source_file_1', 'source_file_2');

        $this->assertTrue(ftp_size($this->connection, $this->workspace.'/source_file_2') != -1);
        $this->assertFalse(ftp_size($this->connection, $this->workspace.'/source_file_1') != -1);
    }

    /**
     * @expectedException \Pagekit\Component\File\Exception\IOException
     */
    public function testRenameFailed()
    {
        $this->adapter->rename('folder1/fileunknown', 'folder1/fileunknown2');
    }

    public function testCopy()
    {
        $temp = fopen('php://temp', 'r+');
        ftp_fput($this->connection, $this->workspace.'/source_file_1', $temp, $this->mode);
        fclose($temp);

        $this->assertTrue(ftp_size($this->connection, $this->workspace.'/source_file_1') != -1);

        $this->adapter->copy('source_file_1', 'source_file_2');

        $this->assertTrue(ftp_size($this->connection, $this->workspace.'/source_file_1') != -1);
        $this->assertTrue(ftp_size($this->connection, $this->workspace.'/source_file_2') != -1);

        $this->setExpectedException('\Pagekit\Component\File\Exception\IOException');

        $this->adapter->copy($this->workspace.'/source_file_1', $this->workspace);
    }

    /**
     * @expectedException \Pagekit\Component\File\Exception\IOException
     */
    public function testCopyFailed()
    {
        $this->adapter->copy('folder1/fileunknown', 'folder1/fileunknown2');
    }

    public function testExists()
    {
        $file = $this->workspace.'/source_file_1';
        $file2 = $this->workspace.'/source_file_2';

        $temp = fopen('php://temp', 'r+');
        ftp_fput($this->connection, $file, $temp, $this->mode);
        fclose($temp);

        $this->assertTrue($this->adapter->exists($file));
        $this->assertFalse($this->adapter->exists($file2));
    }
}
