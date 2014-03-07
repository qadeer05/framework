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
    protected $paramReader;
    protected $paramFetcher;

    /**
     * Constructor.
     *
     * @param ParamReaderInterface  $paramReader
     * @param ParamFetcherInterface $paramFetcher
     */
    public function __construct(ParamReaderInterface $paramReader = null, ParamFetcherInterface $paramFetcher = null)
    {
        $this->paramReader = $paramReader ?: new ParamReader;
        $this->paramFetcher = $paramFetcher ?: new ParamFetcher;
    }

    /**
     * Reads the @Request annotations from the controller stores them in the "_params" attribute.
     *
     * @param ConfigureRouteEvent $event
     */
    public function onConfigureRoute(ConfigureRouteEvent $event)
    {
        $event->getRoute()->setOption('params', $this->paramReader->read($event->getMethod()));
    }

    /**
     * Maps the parameters to request attributes.
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $controller = $event->getController();

        if (is_array($controller) && $params = $request->attributes->get('_route_options[params]')) {

            $this->paramFetcher->setRequest($request);
            $this->paramFetcher->setParameters($params);

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
            'route.configure'   => 'onConfigureRoute',
            'kernel.controller' => 'onKernelController'
        );
    }
}
