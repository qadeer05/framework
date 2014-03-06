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
        return array(
            array(ZipArchive::ER_EXISTS, "File already exists"),
            array(ZipArchive::ER_INCONS, "Zip archive is inconsistent"),
            array(ZipArchive::ER_INVAL, "Invalid argument"),
            array(ZipArchive::ER_MEMORY, "Memory allocation failure"),
            array(ZipArchive::ER_NOENT, "No such ZIP file"),
            array(ZipArchive::ER_NOZIP, "Is not a ZIP archive"),
            array(ZipArchive::ER_OPEN, "Can't open ZIP file"),
            array(ZipArchive::ER_READ, "Zip read error"),
            array(ZipArchive::ER_SEEK, "Zip seek error"),
            array(ZipArchive::ER_MULTIDISK, "Multidisk ZIP Archives not supported"),
            array(ZipArchive::ER_RENAME, "Failed to rename the temporary file for ZIP"),
            array(ZipArchive::ER_CLOSE, "Failed to close the ZIP Archive"),
            array(ZipArchive::ER_WRITE, "Failure while writing the ZIP Archive"),
            array(ZipArchive::ER_CRC, "CRC failure within the ZIP Archive"),
            array(ZipArchive::ER_ZIPCLOSED, "ZIP Archive already closed"),
            array(ZipArchive::ER_TMPOPEN, "Failure creating temporary ZIP Archive"),
            array(ZipArchive::ER_CHANGED, "ZIP Entry has been changed"),
            array(ZipArchive::ER_ZLIB, "ZLib Problem"),
            array(ZipArchive::ER_COMPNOTSUPP, "Compression method not supported within ZLib"),
            array(ZipArchive::ER_EOF, "Premature EOF within ZIP Archive"),
            array(ZipArchive::ER_INTERNAL, "Internal error while working on a ZIP Archive"),
            array(ZipArchive::ER_REMOVE, "Can not remove ZIP Archive"),
            array(ZipArchive::ER_DELETED, "ZIP Entry has been deleted"),
            array('default', "Not a valid ZIP archive, got error code: default")
        );
    }
}