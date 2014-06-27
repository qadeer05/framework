<?php

namespace Pagekit\Framework\Controller;

use Pagekit\Component\Routing\Controller\ControllerResolver as BaseControllerResolver;
use Pagekit\Framework\Application;
use Psr\Log\LoggerInterface;

class ControllerResolver extends BaseControllerResolver
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param Application     $app
     * @param LoggerInterface $logger
     */
    public function __construct(Application $app, LoggerInterface $logger = null)
    {
        parent::__construct($logger);

        $this->app = $app;
    }

    /**
     * Creates a controller instance and injects dependencies.
     *
     * @param  string $controller
     * @throws \InvalidArgumentException
     * @return mixed
     */
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
        }

        list($class, $method) = explode('::', $controller, 2);

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $reflection = new \ReflectionClass($class);

        if ($constructor = $reflection->getConstructor()) {

            $args = array();

            foreach ($constructor->getParameters() as $param) {
                if ($class = $param->getClass()) {

                    if ($class->isInstance($this->app)) {
                        $args[] = $this->app;
                    } elseif ($extension = $this->app['extensions']->get($class->getName())) {
                        $args[] = $extension;
                    }

                } else {
                    throw new \InvalidArgumentException(sprintf('Unknown constructor argument "$%s".', $param->getName()));
                }
            }

            $instance = $reflection->newInstanceArgs($args);
        }

        return array(isset($instance) ? $instance : new $class, $method);
    }
}
