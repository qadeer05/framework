<?php

namespace Pagekit\Framework\Controller;

use Pagekit\Framework\Application;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;

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
     * @{inheritdoc}
     */
    protected function doGetArguments(Request $request, $controller, array $parameters)
    {
        foreach ($parameters as $param) {
            if ($class = $param->getClass()) {
                if ($class->isInstance($this->app)) {
                    $request->attributes->set($param->getName(), $this->app);
                } elseif ($extension = $this->app['extensions']->get($class->getName())) {
                    $request->attributes->set($param->getName(), $extension);
                }
            }
        }

        return parent::doGetArguments($request, $controller, $parameters);
    }
}
