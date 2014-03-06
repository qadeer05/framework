<?php

namespace Pagekit\Component\Package\Tests;

use Pagekit\Component\Package\Installer\PackageInstaller;
use Pagekit\Component\Package\Loader\JsonLoader;
use Pagekit\Component\Package\Repository\InstalledRepository;

/**
 * Test class for FtpPackageInstaller.
 */
class FtpPackageInstallerTest extends \Pagekit\Tests\FtpTestCase
{
    private $filesystem = null;
    private $installer = null;
    private $repository = null;
    private $package = null;

    public function setUp()
    {
        parent::setUp();

        if (!is_resource($this->connection)) {
            return;
        }

        $loader = new JsonLoader;

        if (!$this->package = $loader->load(__DIR__.'/../Fixtures/Package/extension.json')) {
            $this->markTestSkipped('Unable to load package.');
            return;
        }

        $this->repository = new InstalledRepository($this->workspace);
        $this->filesystem = new \Pagekit\Component\File\Filesystem(new \Pagekit\Component\File\Adapter\FtpAdapter($this->workspace, $GLOBALS['ftp_host'], $GLOBALS['ftp_user'], $GLOBALS['ftp_pass'], $GLOBALS['ftp_port'], $GLOBALS['ftp_passive'], $this->mode));
        $this->installer  = new PackageInstaller($this->repository, $loader, $this->filesystem);
    }

    public function testInstall()
    {
        $this->installer->install(__DIR__.'/../Fixtures/Package/extension.json');

        $this->assertTrue($this->repository->hasPackage($this->package));
        $this->assertTrue($this->filesystem->exists('test'));
        $this->assertTrue($this->filesystem->exists('test/extension.json'));
        $this->assertTrue($this->filesystem->exists('test/directory/test'));
    }

    public function testIsInstalled()
    {
        $this->assertFalse($this->installer->isInstalled($this->package));
        $this->installer->install(__DIR__.'/../Fixtures/Package/extension.json');
        $this->assertTrue($this->installer->isInstalled($this->package));
    }

    public function testUpdate()
    {
        $this->installer->install(__DIR__.'/../Fixtures/Package/extension.json');

        $this->assertEquals('0.0.1', $this->repository->findPackage('test')->getVersion());
        $this->installer->update($this->workspace.'/test', __DIR__.'/../Fixtures/Package2');
        $this->assertEquals('0.0.2', $this->repository->findPackage('test')->getVersion());
    }

    public function testUninstall()
    {
        $this->installer->install(__DIR__.'/../Fixtures/Package/extension.json');

        $this->assertTrue($this->repository->hasPackage($this->package));
        $this->installer->uninstall($this->workspace.'/test');
        $this->assertFalse($this->repository->hasPackage($this->package));
        $this->assertFalse($this->filesystem->exists('test'));
    }

    public function testGetInstallPath()
    {
        $this->assertEquals($this->workspace.'/test', $this->repository->getInstallPath($this->package));
    }
}
