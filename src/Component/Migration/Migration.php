<?php

namespace Pagekit\Component\Migration;

class Migration
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $current;

    /**
     * @var string
     */
    protected $pattern;

    /**
     * Constructor.
     *
     * @param string $path
     * @param string $current
     * @param string $pattern
     */
    public function __construct($path, $current = null, $pattern = '/^(\d{4}_\d{2}_\d{2}_\d{6})_(.*)\.php$/')
    {
        $this->path    = $path;
        $this->current = $current;
        $this->pattern = $pattern;
    }

    /**
     * Gets the migrations from this migrations path
     *
     * @param  string|null $start version to start migrations from, or null to start at the beginning
     * @param  string|null $end   version to end migrations at, or null to migrate to the end
     * @return MigrationInterface[]
     */
    public function get($start = null, $end = null)
    {
        $migrations = array();

        if (!is_dir($this->path)) {
            return $migrations;
        }

        foreach (new \DirectoryIterator($this->path) as $file) {

            if (!$file->isFile() || !preg_match($this->pattern, $file->getFilename(), $matches) || ($start !== null && strnatcmp($start, $matches[1]) >= 0) || ($end !== null && strnatcmp($end, $matches[1]) < 0)) {
                continue;
            }

            include_once($file->getPathname());

            $class = $this->findClass($file->getPathname());

            if (!class_exists($class)) {
                continue;
            }

            $migration = new $class;

            if (!$migration instanceof MigrationInterface) {
                continue;
            }

            $migrations[$matches[1]] = $migration;
        }

        uksort($migrations, 'strnatcmp');

        return $migrations;
    }

    /**
     * Migrate up to the next version
     *
     * @param  string|null $version
     * @return array
     */
    public function up($version = null)
    {
        if (!$migrations = $this->get($this->current, $version)) {
            return array();
        }

        // if no version was given, only apply the next migration
        is_null($version) and reset($migrations) and $migrations = array(key($migrations) => current($migrations));

        return $this->apply($migrations);
    }

    /**
     * Migrate down to the previous version
     *
     * @param  string|null $version
     * @return array
     */
    public function down($version = null)
    {
        if (!$migrations = array_reverse($this->get($version, $this->current), true)) {
            return array();
        }

        // if no version was given, only apply the next migration
        is_null($version) and reset($migrations) and $migrations = array(key($migrations) => current($migrations));

        return $this->apply($migrations, 'down');
    }

    /**
     * Migrate to a specific version or range of versions
     *
     * @param  string|null $version
     * @return array
     * @throws \UnexpectedValueException
     */
    public function version($version = null)
    {
        // determine the direction
        if (is_null($version) or is_null($this->current) or strnatcmp($this->current, $version) < 0) {
            return $this->up($version);
        } else {
            return $this->down($version);
        }
    }

    /**
     * Migrate to the latest version
     *
     * @return array
     */
    public function latest()
    {
        if (!$migrations = $this->get($this->current)) {
            return array();
        }
        return $this->apply($migrations);
    }

    /**
     * Applies a method to migrations
     *
     * @param  MigrationInterface[] $migrations
     * @param  string               $method
     * @return array
     */
    public function apply(array $migrations, $method = 'up')
    {
        foreach ($migrations as $migration) {
            $migration->$method();
        }
        return array_keys($migrations);
    }

    /**
     * Returns the full class name for the first class in the file.
     *
     * @param  string $file
     * @return string|false
     */
    protected function findClass($file)
    {
        $class     = false;
        $namespace = false;
        $tokens    = token_get_all(file_get_contents($file));

        for ($i = 0, $count = count($tokens); $i < $count; $i++) {

            $token = $tokens[$i];

            if (!is_array($token)) {
                continue;
            }

            if (true === $class && T_STRING === $token[0]) {
                return $namespace . '\\' . $token[1];
            }

            if (true === $namespace && T_STRING === $token[0]) {
                $namespace = '';
                do {
                    $namespace .= $token[1];
                    $token = $tokens[++$i];
                } while ($i < $count && is_array($token) && in_array($token[0], array(T_NS_SEPARATOR, T_STRING)));
            }

            if (T_CLASS === $token[0]) {
                $class = true;
            }

            if (T_NAMESPACE === $token[0]) {
                $namespace = true;
            }
        }

        return false;
    }
}
