<?php

namespace Pagekit\Component\Package\Tests\Repository;

use Pagekit\Component\Package\Package;
use Pagekit\Component\Package\Repository\RemoteRepository;

class RemoteRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialize()
    {
        $repo = $this->getRepository(file_get_contents(__DIR__.'/../Fixtures/Package/extension.json'));

        $this->assertEquals(1, count($repo));
        $this->assertTrue($repo->hasPackage(new Package('test', '0.0.1', '0.0.1')));
    }

    public function testFindPackage()
    {
        $repo = $this->getRepository(file_get_contents(__DIR__.'/../Fixtures/Package/extension.json'));
        $this->assertInstanceOf('Pagekit\Component\Package\PackageInterface', $repo->findPackage('test', '0.0.1'));
        $this->assertInstanceOf('Pagekit\Component\Package\PackageInterface', $repo->findPackage('test'));
    }

    public function testFindPackages()
    {
        $repo = $this->getRepository(file_get_contents(__DIR__.'/../Fixtures/repository.json'));
        $this->assertCount(1, $repo->findPackages('test'));
    }

    /**
     * @param type $body
     * @return \Pagekit\Component\Package\Repository\RemoteRepository
     */
    protected function getRepository($body = '[]')
    {
        $response = $this->getMock('Guzzle\Http\Message\Response', array(), array(200));
        $response->expects($this->any())
               ->method('getBody')
               ->will($this->returnValue($body));

        $client = $this->getMock('Pagekit\Component\Http\Client');
        $client->expects($this->any())
               ->method('get')
               ->will($this->returnValue($response))
               ->method('send')
               ->will($this->returnValue($response));

        return new RemoteRepository('', null, $client);
    }
}