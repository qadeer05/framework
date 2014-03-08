<?php

namespace Pagekit\Component\Routing\Loader;

use Pagekit\Component\Routing\Controller\ControllerReaderInterface;
use Symfony\Component\Routing\RouteCollection;

class RouteLoader implements LoaderInterface
{
    /**
     * @var ControllerReaderInterface
     */
    protected $reader;

    /**
     * Constructor.
     *
     * @param ControllerReaderInterface $reader
     */
    public function __construct(ControllerReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function load($controller, array $options = array())
    {
        $class = null;

        if ('.php' == substr($controller, -4) && file_exists($controller)) {
            $class = $this->findClass($controller);
        } elseif (class_exists($controller)) {
            $class = $controller;
        }

        if (empty($class)) {
            throw new \InvalidArgumentException(sprintf('Controller class "%s" does not exist.', $controller));
        }

        return $this->reader->read(new \ReflectionClass($class), $options);
    }

    /**
     * Returns the full class name for the first class in the file.
     *
     * @param  string  $file
     * @return string|false
     */
    protected function findClass($file)
    {
        $class = false;
        $namespace = false;
        $tokens = token_get_all(file_get_contents($file));

        for ($i = 0, $count = count($tokens); $i < $count; $i++) {

            $token = $tokens[$i];

            if (!is_array($token)) {
                continue;
            }

            if (true === $class && T_STRING === $token[0]) {
                return $namespace.'\\'.$token[1];
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
