<?php

namespace Pagekit\Framework\Extension;

use Pagekit\Framework\Extension\Exception\ExtensionLoadException;
use Pagekit\Framework\Extension\Exception\InvalidNameException;
use Pagekit\Framework\Package\PackageManager;

class ExtensionManager extends PackageManager
{
    /**
     * @var array
     */
    protected $classes = array();

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if (isset($this->loaded[$name])) {
            return $this->loaded[$name];
        }

        if (isset($this->classes[$name])) {
            return $this->classes[$name];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($name, $path = null)
    {
        $root = $path ?: $this->repository->getPath()."/$name";

        if (!is_string($name)) {
            throw new InvalidNameException('Extension name must be of type string.');
        }

        if (isset($this->loaded[$name])) {
            throw new ExtensionLoadException(sprintf('Extension already loaded %s.', $name));
        }

        if (!file_exists("$root/extension.php")) {
            throw new ExtensionLoadException(sprintf('Extension path does not exist (%s).', $root));
        }

        $fn = function($app, $bootstrap) {
            return include($bootstrap);
        };

        $config = (!($config = $fn($this->app, "$root/extension.php")) || 1 === $config) ? array() : $config;
        $class  = isset($config['main']) ? $config['main'] : 'Pagekit\Framework\Extension\Extension';

        if (isset($config['autoload'])) {
            foreach ($config['autoload'] as $namespace => $path) {
                $this->autoloader->addPsr4($namespace, "$root/$path");
            }
        }

        return $this->loaded[$name] = $this->classes[$class] = new $class($name, $root, $config);
    }
}
