<?php

namespace Pagekit\Component\File\Tests;

use Pagekit\Component\File\Adapter\FilesystemAdapter;
use Pagekit\Component\File\Filesystem;

/**
 * Test class for FilesystemAdapter.
 */
class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    use \Pagekit\Tests\FileUtil;

    /**
     * @var string
     */
    protected $workspace;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function setUp()
    {
        $this->workspace = $this->getTempDir('file_filesystem_');
        $this->filesystem = new Filesystem(new FilesystemAdapter($this->workspace));
    }

    public function tearDown()
    {
        $this->removeDir($this->workspace);
    }

    public function testGetSize()
    {
        $sizeFilePath = $this->workspace.'/size_source_file';

        file_put_contents($sizeFilePath, 'foo');
        $this->assertEquals(3, $this->filesystem->getSize($sizeFilePath));
    }

    public function testGetModified()
    {
        $modifiedFilePath = $this->workspace.'/modified_source_file';

        touch($modifiedFilePath);
        $this->assertEquals(filemtime($modifiedFilePath), $this->filesystem->getModified($modifiedFilePath));
    }

    public function testAppend()
    {
        $appendFilePath = $this->workspace.'/append_source_file';

        $this->filesystem->putContents($appendFilePath, 'foo', true);

        $this->assertFileExists($appendFilePath);
        $this->assertEquals('foo', file_get_contents($appendFilePath));

        $this->filesystem->putContents($appendFilePath, 'bar', true);
        $this->assertEquals('foobar', file_get_contents($appendFilePath));
    }

    public function testAppendFail()
    {
        // Test exception
        try {
            $this->filesystem->putContents($this->workspace, 'can not append to directories', true);
        } catch (\Exception $e) {
            $this->assertEquals('file_put_contents('.$this->workspace.'): failed to open stream: Is a directory', $e->getMessage());
        }
    }

    public function testRead()
    {
        $contentFilePath = $this->workspace.'/content_source_file';

        file_put_contents($contentFilePath, 'foo');
        $this->assertFileExists($contentFilePath);
        $this->assertEquals('foo', $this->filesystem->getContents($contentFilePath));

        // Test exception
        $this->assertFalse($this->filesystem->getContents($contentFilePath.'doesnotexist'));
    }

    public function testMkdir()
    {
        $directory = $this->workspace.'/folder1/folder2';
        $this->filesystem->makeDir($directory, 0755, true);

        $this->assertTrue(is_dir($directory));
    }

    public function testDelete()
    {
        $directory = $this->workspace.'/testRmdir1/testRmdir2';
        mkdir($directory, 0755, true);
        touch($directory.'/source_file_1');
        touch($directory.'/source_file_2');
        touch($directory.'/source_file_3');

        mkdir($directory.'/source_dir_1');
        mkdir($directory.'/source_dir_2');
        touch($directory.'/source_dir_2/source_file_3');

        $this->assertTrue(is_dir($directory));

        $this->filesystem->delete($directory);

        $this->assertFalse(is_dir($directory));
    }

    public function testDeleteFailed()
    {
        $this->assertFalse($this->filesystem->delete('folder1/folder2unknown'));
    }

    public function testRename()
    {
        $file = $this->workspace.'/source_file_1';
        $file2 = $this->workspace.'/source_file_2';
        touch($file);
        $this->assertTrue(is_file($file));

        $this->filesystem->rename($file, $file2);

        $this->assertTrue(is_file($file2));
        $this->assertFalse(is_file($file));
    }

    public function testRenameFailed()
    {
        try {
            $this->filesystem->rename($this->workspace.'/folder1/fileunknown', $this->workspace.'/folder1/fileunknown2');
        } catch (\Exception $e) {
            $this->assertEquals('rename('.$this->workspace.'/folder1/fileunknown,'.$this->workspace.'/folder1/fileunknown2): No such file or directory', $e->getMessage());
        }
    }

    public function testCopy()
    {
        $file = $this->workspace.'/source_file_1';
        $file2 = $this->workspace.'/source_file_2';
        touch($file);
        $this->assertTrue(is_file($file));
        $this->assertFalse(is_file($file2));

        $this->filesystem->copy($file, $file2);

        $this->assertTrue(is_file($file));
        $this->assertTrue(is_file($file2));
    }

    public function testCopyFailed()
    {
        try {
            $this->filesystem->copy($this->workspace.'/folder1/fileunknown', $this->workspace.'/folder1/fileunknown2');
        } catch (\Exception $e) {
            $this->assertEquals('copy('.$this->workspace.'/folder1/fileunknown): failed to open stream: No such file or directory', $e->getMessage());
        }
    }

    public function testExists()
    {
        $file = $this->workspace.'/source_file_1';
        $file2 = $this->workspace.'/source_file_2';
        touch($file);
        $this->assertTrue($this->filesystem->exists($file));
        $this->assertFalse($this->filesystem->exists($file2));
    }

    public function testCopyDir()
    {
        $source = $this->workspace.'/testCopydir1/testCopydir2';
        $target = $this->workspace.'/targetDir';
        mkdir($source, 0755, true);
        touch($source.'/source_file_1');
        touch($source.'/source_file_2');
        touch($source.'/source_file_3');

        mkdir($source.'/source_dir_1');
        mkdir($source.'/source_dir_2');
        touch($source.'/source_dir_2/source_file_3');

        $this->assertTrue(is_dir($source));
        $this->assertFalse(is_dir($target));

        $this->filesystem->copyDir($source, $target);

        $this->assertTrue(is_dir($target));
        $this->assertTrue(is_dir($target.'/source_dir_1'));
        $this->assertTrue(is_dir($target.'/source_dir_2'));
        $this->assertTrue(is_file($target.'/source_dir_2/source_file_3'));
    }

    public function testChmodChangesFileMode()
    {
        $this->markAsSkippedIfChmodIsMissing();

        $dir = $this->workspace.DIRECTORY_SEPARATOR.'dir';
        mkdir($dir);
        $file = $dir.DIRECTORY_SEPARATOR.'file';
        touch($file);

        $this->filesystem->changeMode($file, 0400);
        $this->filesystem->changeMode($dir, 0753);

        $this->assertEquals(753, $this->getFilePermissions($dir));
        $this->assertEquals(400, $this->getFilePermissions($file));
    }

    public function testChmodRecursive()
    {
        $this->markAsSkippedIfChmodIsMissing();

        $dir = $this->workspace.DIRECTORY_SEPARATOR.'dir';
        mkdir($dir);

        $files = ['file1', 'file2', 'file3'];
        foreach ($files as $file) {
            touch($dir.DIRECTORY_SEPARATOR.$file);
        }

        $this->filesystem->changeMode($dir, 0777, 0000, true);
        foreach ($files as $file) {
            $this->assertEquals(777, $this->getFilePermissions($dir.DIRECTORY_SEPARATOR.$file));
        }
    }

    public function testChmodAppliesUmask()
    {
        $this->markAsSkippedIfChmodIsMissing();

        $file = $this->workspace.DIRECTORY_SEPARATOR.'file';
        touch($file);

        $this->filesystem->changeMode($file, 0770, 0022);
        $this->assertEquals(750, $this->getFilePermissions($file));
    }

    public function testChmodChangesModeOfArrayOfFiles()
    {
        $this->markAsSkippedIfChmodIsMissing();

        $directory = $this->workspace.DIRECTORY_SEPARATOR.'directory';
        $file = $this->workspace.DIRECTORY_SEPARATOR.'file';
        $files = [$directory, $file];

        mkdir($directory);
        touch($file);

        $this->filesystem->changeMode($files, 0753);

        $this->assertEquals(753, $this->getFilePermissions($file));
        $this->assertEquals(753, $this->getFilePermissions($directory));
    }

    public function testChmodChangesModeOfTraversableFileObject()
    {
        $this->markAsSkippedIfChmodIsMissing();

        $directory = $this->workspace.DIRECTORY_SEPARATOR.'directory';
        $file = $this->workspace.DIRECTORY_SEPARATOR.'file';
        $files = new \ArrayObject([$directory, $file]);

        mkdir($directory);
        touch($file);

        $this->filesystem->changeMode($files, 0753);

        $this->assertEquals(753, $this->getFilePermissions($file));
        $this->assertEquals(753, $this->getFilePermissions($directory));
    }

    public function testChown()
    {
        $this->markAsSkippedIfPosixIsMissing();

        $dir = $this->workspace.DIRECTORY_SEPARATOR.'dir';
        mkdir($dir);

        $this->filesystem->changeOwner($dir, $this->getFileOwner($dir));
    }

    public function testChownRecursive()
    {
        $this->markAsSkippedIfPosixIsMissing();

        $dir = $this->workspace.DIRECTORY_SEPARATOR.'dir';
        mkdir($dir);
        $file = $dir.DIRECTORY_SEPARATOR.'file';
        touch($file);

        $this->filesystem->changeOwner($dir, $this->getFileOwner($dir), true);
    }

    public function testChownFail()
    {
        $this->markAsSkippedIfPosixIsMissing();

        $dir = $this->workspace.DIRECTORY_SEPARATOR.'dir';
        mkdir($dir);
        $user = 'user' . time() . mt_rand(1000, 9999);

        try {
            $this->filesystem->changeOwner($dir, $user);
        } catch (\Exception $e) {
            $this->assertEquals('chown(): Unable to find uid for '.$user, $e->getMessage());
        }
    }

    public function testChgrp()
    {
        $this->markAsSkippedIfPosixIsMissing();

        $dir = $this->workspace.DIRECTORY_SEPARATOR.'dir';
        mkdir($dir);

        $this->filesystem->changeGroup($dir, $this->getFileGroup($dir));
    }

    public function testChgrpRecursive()
    {
        $this->markAsSkippedIfPosixIsMissing();

        $dir = $this->workspace.DIRECTORY_SEPARATOR.'dir';
        mkdir($dir);
        $file = $dir.DIRECTORY_SEPARATOR.'file';
        touch($file);

        $this->filesystem->changeGroup($dir, $this->getFileGroup($dir), true);
    }

    public function testChgrpFail()
    {
        $this->markAsSkippedIfPosixIsMissing();

        $dir = $this->workspace.DIRECTORY_SEPARATOR.'dir';
        mkdir($dir);
        $user = 'user' . time() . mt_rand(1000, 9999);

        try {
            $this->filesystem->changeGroup($dir, $user);
        } catch (\Exception $e) {
            $this->assertEquals('chgrp(): Unable to find gid for '.$user, $e->getMessage());
        }
    }

    /**
     * Returns file permissions as three digits (i.e. 755)
     *
     * @param string $filePath
     *
     * @return integer
     */
    private function getFilePermissions($filePath)
    {
        return (int) substr(sprintf('%o', fileperms($filePath)), -3);
    }

    private function getFileOwner($filepath)
    {
        $this->markAsSkippedIfPosixIsMissing();

        $infos = stat($filepath);
        if ($datas = posix_getpwuid($infos['uid'])) {
            return $datas['name'];
        }
    }

    private function getFileGroup($filepath)
    {
        $this->markAsSkippedIfPosixIsMissing();

        $infos = stat($filepath);
        if ($datas = posix_getgrgid($infos['gid'])) {
            return $datas['name'];
        }
    }

    /**
     * TODO: unused
     */
    private function markAsSkippedIfSymlinkIsMissing()
    {
        if (!function_exists('symlink')) {
            $this->markTestSkipped('symlink is not supported');
        }

        if (defined('PHP_WINDOWS_VERSION_MAJOR') && false === self::$symlinkOnWindows) {
            $this->markTestSkipped('symlink requires "Create symbolic links" privilege on windows');
        }
    }

    private function markAsSkippedIfChmodIsMissing()
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('chmod is not supported on windows');
        }
    }

    private function markAsSkippedIfPosixIsMissing()
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR') || !function_exists('posix_isatty')) {
            $this->markTestSkipped('Posix is not supported');
        }
    }
}