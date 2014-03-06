<?php

namespace Pagekit\Component\Package\Tests;

use Pagekit\Component\Package\Package;

class PackageTest extends \PHPUnit_Framework_TestCase
{
    public function testInstallationSource()
    {
        $package = new Package('foo', '1', '1');
        $package->setInstallationSource('dist');

        $this->assertEquals('dist', $package->getInstallationSource());
    }

    /**
     * @dataProvider providerVersioningSchemes
     */
    public function testPackageHasExpectedNamingSemantics($name, $version)
    {
        $package = new Package($name, $version, $version);

        $this->assertEquals(strtolower($name), $package->getName());
    }

    /**
     * @dataProvider providerVersioningSchemes
     */
    public function testPackageHasExpectedVersioningSemantics($name, $version)
    {
        $package = new Package($name, $version, $version);

        $this->assertEquals($version, $package->getVersion());
    }

    /**
     * @dataProvider providerVersioningSchemes
     */
    public function testPackageHasExpectedMarshallingSemantics($name, $version)
    {
        $package = new Package($name, $version, $version);

        $this->assertEquals(strtolower($name).'-'.$version, (string) $package);
    }

    public function providerVersioningSchemes()
    {
        $provider[] = array('foo',            '1-beta');
        $provider[] = array('node',           '0.5.6');
        $provider[] = array('li3',            '0.10');
        $provider[] = array('mongodb_odm',    '1.0.0BETA3');
        $provider[] = array('DoctrineCommon', '2.2.0-DEV');

        return $provider;
    }
}