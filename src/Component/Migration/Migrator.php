<?php

namespace Pagekit\Component\Migration;

class Migrator
{
    /**
     * Run the outstanding migrations at a given path.
     *
     * @param  string $path
     * @param  string $current
     * @param  string $pattern
     * @throws \InvalidArgumentException
     * @return string|boolean
     */
    public function run($path, $current = null, $pattern = '/^(\d{4}_\d{2}_\d{2}_\d{6})_(.*)\.php$/')
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('Unable to run migrations. Could not find path "%s"', $path));
        }

        $migration = new Migration($path, $current, $pattern);

        if ($result = $migration->latest()) {
            return end($result);
        }

        return false;
    }

    /**
     * Returns the outstanding migrations at a given path.
     *
     * @param  string $path
     * @param  string $current
     * @param  string $pattern
     * @throws \InvalidArgumentException
     * @return MigrationInterface[]
     */
    public function get($path, $current = null, $pattern = '/^(\d{4}_\d{2}_\d{2}_\d{6})_(.*)\.php$/')
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('Unable to locate migrations. Couldn\'t find path "%s"', $path));
        }

        $migration = new Migration($path, $current, $pattern);

        return $migration->get($current);
    }
}
