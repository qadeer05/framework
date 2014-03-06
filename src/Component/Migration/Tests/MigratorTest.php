<?php

namespace Pagekit\Component\Migration\Tests;

use Pagekit\Component\Migration\Migrator;

/**
 * Test class for Migrations.
 */
class MigratorTest extends \PHPUnit_Framework_TestCase
{
	public function testRun() {
		$migrator = new Migrator;
		$this->assertEquals('0000_00_00_000007', $migrator->run(__DIR__.'/Fixtures'));
	}

	public function testRunException() {
		$this->setExpectedException('InvalidArgumentException');
		$migrator = new Migrator;
		$migrator->run(__DIR__.'/invalidPath');
	}

	public function testGet() {
		$migrator = new Migrator;
		$this->assertCount(6, $migrator->get(__DIR__.'/Fixtures'));

		$migrator = new Migrator;
		$this->assertCount(2, $migrator->get(__DIR__.'/Fixtures', '0000_00_00_000003'));
	}

	public function testGetException () {
		$this->setExpectedException('InvalidArgumentException');
		$migrator = new Migrator;
		$migrator->get(__DIR__.'/invalidPath');
	}
}