<?php

namespace Pagekit\Component\Migration;

interface MigrationInterface
{
	/**
	 * Migrate up to the next version
	 */
    public function up();

	/**
	 * Migrate down to the previous version
	 */
    public function down();
}