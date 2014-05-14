<?php

namespace Pagekit\Component\Routing\EventListener;

use Pagekit\Component\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OptionsListener implements EventSubscriberInterface
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * Constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Adds the route's options to the request attributes.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$route = $this->router->getRoute($request->attributes->get('_route'))) {
            return;
        }

        $request->attributes->set('_route_options', $route->getOptions());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 30)
        );
    }
}
