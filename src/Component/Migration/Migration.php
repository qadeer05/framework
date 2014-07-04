<?php

namespace Pagekit\Component\Migration;

class Migration
{
    /**
     * @var string
     */
    protected $current;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var array
     */
    protected $files = [];

    /**
     * Constructor.
     *
     * @param Migrator $migrator
     * @param string   $path
     * @param string   $current
     * @param array    $parameters
     */
    public function __construct(Migrator $migrator, $path, $current = null, $parameters = [])
    {
        $this->current    = $current;
        $this->parameters = array_replace($migrator->getGlobals(), $parameters);
        $this->files      = $this->load($path, $migrator->getPattern());
    }

    /**
     * Migrate up to a version.
     *
     * @param  string|null $version
     * @return array
     */
    public function up($version = null)
    {
        return $this->apply($this->current, $version);
    }

    /**
     * Migrate down to a version.
     *
     * @param  string|null $version
     * @return array
     */
    public function down($version = null)
    {
        return $this->apply($version, $this->current, 'down');
    }

    /**
     * Applies migrations.
     *
     * @param  string|null $start
     * @param  string|null $end
     * @param  string      $method
     * @return string|bool
     */
    protected function apply($start = null, $end = null, $method = 'up')
    {
        $files = [];
        $value = false;

        foreach ($this->files as $version => $file) {

            if (($start !== null && strnatcmp($start, $version) >= 0) || ($end !== null && strnatcmp($end, $version) < 0)) {
                continue;
            }

            $files[$version] = $file;
        }

        if ($method == 'down') {
            $files = array_reverse($files, true);
        }

        foreach ($files as $version => $file) {

            extract($this->parameters, EXTR_SKIP);

            $value  = $version;
            $config = require $file;

            if (is_array($config) && isset($config[$method])) {
                call_user_func($config[$method]);
            }
        }

        return $value;
    }

    /**
     * Loads all migration files form a given path.
     *
     * @param  string $path
     * @param  string $pattern
     * @throws \InvalidArgumentException
     * @return array
     */
    protected function load($path, $pattern)
    {
        $files = [];

        if (!is_dir($path)) {
            throw new \InvalidArgumentException(sprintf('Unable to run migrations. Could not find path "%s"', $path));
        }

        foreach (new \DirectoryIterator($path) as $file) {
            if ($file->isFile() && preg_match($pattern, $file->getFilename(), $matches)) {
                $files[$matches['version']] = $file->getPathname();
            }
        }

        uksort($files, 'strnatcmp');

        return $files;
    }
}
