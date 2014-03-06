<?php

namespace Pagekit\Component\Migration\Tests;

use Pagekit\Component\Migration\Migration;

/**
 * Test class for Migrations.
 */
class MigrationTest extends \PHPUnit_Framework_TestCase
{
    public function testUp()
    {
        $migration = new Migration(__DIR__.'/Fixtures');
        $this->assertEquals(array('0000_00_00_000000'), $migration->up());
        $this->assertEquals(array('0000_00_00_000000', '0000_00_00_000001', '0000_00_00_000002', '0000_00_00_000003'), $migration->up('0000_00_00_000003'));

        $migration = new Migration(__DIR__.'/Fixtures', '0000_00_00_000000');
        $this->assertEquals(array('0000_00_00_000001'), $migration->up());
        $this->assertEquals(array('0000_00_00_000001', '0000_00_00_000002', '0000_00_00_000003'), $migration->up('0000_00_00_000003'));

        $migration = new Migration(__DIR__.'/Fixtures', '0000_00_00_000003');
        $this->assertCount(0, $migration->up('0000_00_00_000003'));
    }

    public function testDown()
    {
        $migration = new Migration(__DIR__.'/Fixtures', '0000_00_00_000003');
        $this->assertEquals(array('0000_00_00_000003'), $migration->down());
        $this->assertEquals(array('0000_00_00_000003', '0000_00_00_000002'), $migration->down('0000_00_00_000001'));

        $migration = new Migration(__DIR__.'/Fixtures', '0000_00_00_000003');
        $this->assertCount(0, $migration->down('0000_00_00_000003'));
    }

    public function testVersion()
    {
        $migration = new Migration(__DIR__.'/Fixtures');
        $this->assertEquals(array('0000_00_00_000000', '0000_00_00_000001', '0000_00_00_000002'), $migration->version('0000_00_00_000002'));
        $this->assertEquals(array('0000_00_00_000000'), $migration->version());

        $migration = new Migration(__DIR__.'/Fixtures', '0000_00_00_000002');
        $this->assertEquals(array('0000_00_00_000002', '0000_00_00_000001'), $migration->version('0000_00_00_000000'));
        $this->assertEquals(array('0000_00_00_000003', '0000_00_00_000006', '0000_00_00_000007'), $migration->version('0000_00_00_000007'));
    }

    public function testLatest()
    {
        $migration = new Migration(__DIR__.'/Fixtures');
        $this->assertEquals(array('0000_00_00_000000', '0000_00_00_000001', '0000_00_00_000002', '0000_00_00_000003', '0000_00_00_000006', '0000_00_00_000007'), $migration->latest());

        $migration = new Migration(__DIR__.'/Fixtures', '0000_00_00_000002');
        $this->assertEquals(array('0000_00_00_000003', '0000_00_00_000006', '0000_00_00_000007'), $migration->latest());

        $migration = new Migration(__DIR__.'/Fixtures', '0000_00_00_000007');
        $this->assertCount(0, $migration->latest());
    }
}
