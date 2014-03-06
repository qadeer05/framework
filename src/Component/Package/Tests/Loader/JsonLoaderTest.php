<?php

namespace Pagekit\Component\Package\Tests\Loader;

use Pagekit\Component\Package\Loader\JsonLoader;

class JsonLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $loader;

    public function setUp()
    {
        $this->loader = new JsonLoader;
    }

    /**
     * @dataProvider getKeys
     */
    public function testLoadFromFile($key, $value)
    {
        $package = $this->loader->load(__DIR__.'/../Fixtures/Package/extension.json');

        $this->assertEquals($value, call_user_func(array($package, 'get'.ucfirst($key))));
    }

    /**
     * @dataProvider getKeys
     */
    public function testLoadFromString($key, $value)
    {
        $package = $this->loader->load(file_get_contents(__DIR__.'/../Fixtures/Package/extension.json'));

        $this->assertEquals($value, call_user_func(array($package, 'get'.ucfirst($key))));
    }

    public function getKeys()
    {
        return array(
            array(
                'name',
                'test'
            ),
            array(
                'version',
                '0.0.1'
            ),
            array(
                'type',
                'extension'
            ),
            array(
                'title',
                'Test'
            ),
            array(
                'authors',
                null
            ),
            array(
                'homepage',
                'http://pagekit.com'
            ),
            array(
                'description',
                'Test Extension Package ...'
            ),
            array(
                'license',
                array('MIT')
            )
        );
    }    
}