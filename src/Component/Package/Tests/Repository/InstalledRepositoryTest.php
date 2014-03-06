<?php

namespace Pagekit\Component\Package\Tests\Repository;

use Pagekit\Component\Package\Repository\InstalledRepository;

class InstalledRepositoryTest extends RepositoryTest
{
    public function testGetInstallPath()
    {
        $package = new \Pagekit\Component\Package\Package('test', '0.0.1', '0.0.1');
        $this->assertEquals('/testpath/test', $this->getRepository()->getInstallPath($package));
    }

    protected function getRepository()
    {
        return new InstalledRepository('/testpath');
    }
}