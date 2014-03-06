<?php

namespace Pagekit\Component\Cache\Tests;

use Doctrine\Common\Cache\ArrayCache;
use Pagekit\Component\Cache\Cache;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cache
     */
    protected $cache;

    public function setUp()
    {
        $this->cache = new Cache(new ArrayCache());
    }

    public function testSaveDeleteContains()
    {
        $this->assertFalse($this->cache->contains('foo'));
        $this->cache->save('foo', 'bar');
        $this->assertTrue($this->cache->contains('foo'));
        $this->assertEquals($this->cache->fetch('foo'), 'bar');
        $this->cache->delete('foo');
        $this->assertFalse($this->cache->contains('foo'));
    }

    public function testFlushAll()
    {
        $this->assertFalse($this->cache->contains('foo'));
        $this->cache->save('foo', 'bar');
        $this->assertTrue($this->cache->contains('foo'));
        $this->cache->flushAll();
        $this->assertFalse($this->cache->contains('foo'));
    }

    public function testNamespace()
    {
        $this->cache->setNamespace('foo_namespace');
        $this->assertEquals('foo_namespace', $this->cache->getNamespace());
    }

    public function testSupports()
    {
        $this->assertNotNull($this->cache->supports());
        $this->assertTrue($this->cache->supports('file'));
        $this->assertTrue($this->cache->supports('array'));
        $this->assertFalse($this->cache->supports('SomethingStupid'));
    }

    public function testStats()
    {
        // ArrayCache doesn't offer any stats, so null is the expected results
        $stats = $this->cache->getStats();
        $this->assertTrue( $stats === null || gettype($stats) == "array");
    }
}
