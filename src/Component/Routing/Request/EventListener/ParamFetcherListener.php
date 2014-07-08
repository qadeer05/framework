<?php

namespace Pagekit\Component\Routing\Request\EventListener;

use Pagekit\Component\Routing\Event\ConfigureRouteEvent;
use Pagekit\Component\Routing\Request\ParamFetcher;
use Pagekit\Component\Routing\Request\ParamFetcherInterface;
use Pagekit\Component\Routing\Request\ParamReader;
use Pagekit\Component\Routing\Request\ParamReaderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ParamFetcherListener implements EventSubscriberInterface
{
    protected $paramFetcher;

    /**
     * Constructor.
     *
     * @param ParamFetcherInterface $paramFetcher
     */
    public function __construct(ParamFetcherInterface $paramFetcher = null)
    {
        $this->paramFetcher = $paramFetcher ?: new ParamFetcher;
    }

    /**
     * Maps the parameters to request attributes.
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request    = $event->getRequest();
        $controller = $event->getController();
        $parameters = $request->attributes->get('_request[value]', array(), true);
        $options    = $request->attributes->get('_request[options]', array(), true);

        if (is_array($controller) && is_array($parameters)) {

            $this->paramFetcher->setRequest($request);
            $this->paramFetcher->setParameters($parameters, $options);

            $r = new \ReflectionMethod($controller[0], $controller[1]);

            foreach ($r->getParameters() as $index => $param) {
                if (null !== $value = $this->paramFetcher->get($index)) {
                    $request->attributes->set($param->getName(), $value);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'kernel.controller' => 'onKernelController'
        );
    }
}
