<?php

namespace Pagekit\Component\Routing\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;

class ControllerResolver extends BaseControllerResolver
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var callable[]
     */
    protected $callbacks = array();

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        parent::__construct($logger);

        $this->logger = $logger;
    }

    /**
     * @param string   $name
     * @param callable $callback
     */
    public function addCallback($name, $callback)
    {
        $this->callbacks[$name] = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function getController(Request $request)
    {
        if (!$controller = $request->attributes->get('_controller')) {
            if (null !== $this->logger) {
                $this->logger->warning('Unable to look for the controller as the "_controller" parameter is missing');
            }

            return false;
        }

        if (0 === strpos($controller, '::') && $name = substr($controller, 2) and isset($this->callbacks[$name])) {
            return $this->callbacks[$name];
        }

        return parent::getController($request);
    }
}
