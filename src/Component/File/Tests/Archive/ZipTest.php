<?php

namespace Pagekit\Component\File\Tests\Archive;

use Pagekit\Component\File\Archive\Zip;
use ZipArchive;

/**
 * Zip Test class.
 */
class ZipTest extends \PHPUnit_Framework_TestCase
{
    use \Pagekit\Tests\FileUtil;

    /**
     * @var string
     */
    protected $workspace;

    /**
     * @var Zip
     */
    protected $zip;

    public function setUp()
    {
        if (!class_exists('ZipArchive')) {
            $this->markTestSkipped('zip extension missing');
        }

        $this->workspace = $this->getTempDir('zip_');
        $this->zip = new Zip;
    }

    public function tearDown()
    {
        $this->removeDir($this->workspace);
    }

    public function testExtract()
    {
        // create test zip
        $zipFile = $this->workspace.'/test.zip';
        $zipArchive = new \ZipArchive;
        if (!$zipArchive->open($zipFile, \ZIPARCHIVE::OVERWRITE)) {
            $this->markTestIncomplete(sprintf('Unable to open zip archive at %s.', $zipFile));
        }
        $zipArchive->addFile(__DIR__.'/Fixtures/test', 'test');
        if (!$zipArchive->status == \ZIPARCHIVE::ER_OK) {
            $this->markTestIncomplete(sprintf('Unable to build zip archive at %s.', $zipFile));
        }
        $zipArchive->close();

        $this->zip->extract($zipFile, $this->workspace.'/testFolder');
        $this->assertFileExists($this->workspace.'/testFolder/test');
    }

    /**
     * @dataProvider provideErrorCodes
     */
    public function testErrorMessages($error, $message)
    {
        $method = new \ReflectionMethod($this->zip, 'getErrorMessage');
        $method->setAccessible(true);

        $this->assertEquals($message, $method->invoke($this->zip, $error, ''));
    }

    public function provideErrorCodes()
    {
        return [
            [ZipArchive::ER_EXISTS, "File already exists"],
            [ZipArchive::ER_INCONS, "Zip archive is inconsistent"],
            [ZipArchive::ER_INVAL, "Invalid argument"],
            [ZipArchive::ER_MEMORY, "Memory allocation failure"],
            [ZipArchive::ER_NOENT, "No such ZIP file"],
            [ZipArchive::ER_NOZIP, "Is not a ZIP archive"],
            [ZipArchive::ER_OPEN, "Can't open ZIP file"],
            [ZipArchive::ER_READ, "Zip read error"],
            [ZipArchive::ER_SEEK, "Zip seek error"],
            [ZipArchive::ER_MULTIDISK, "Multidisk ZIP Archives not supported"],
            [ZipArchive::ER_RENAME, "Failed to rename the temporary file for ZIP"],
            [ZipArchive::ER_CLOSE, "Failed to close the ZIP Archive"],
            [ZipArchive::ER_WRITE, "Failure while writing the ZIP Archive"],
            [ZipArchive::ER_CRC, "CRC failure within the ZIP Archive"],
            [ZipArchive::ER_ZIPCLOSED, "ZIP Archive already closed"],
            [ZipArchive::ER_TMPOPEN, "Failure creating temporary ZIP Archive"],
            [ZipArchive::ER_CHANGED, "ZIP Entry has been changed"],
            [ZipArchive::ER_ZLIB, "ZLib Problem"],
            [ZipArchive::ER_COMPNOTSUPP, "Compression method not supported within ZLib"],
            [ZipArchive::ER_EOF, "Premature EOF within ZIP Archive"],
            [ZipArchive::ER_INTERNAL, "Internal error while working on a ZIP Archive"],
            [ZipArchive::ER_REMOVE, "Can not remove ZIP Archive"],
            [ZipArchive::ER_DELETED, "ZIP Entry has been deleted"],
            ['default', "Not a valid ZIP archive, got error code: default"]
        ];
    }
}