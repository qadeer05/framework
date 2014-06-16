<?php

namespace Pagekit\Component\Routing\EventListener;

use Pagekit\Component\Routing\Event\GenerateRouteEvent;
use Pagekit\Component\Routing\Router;
use Pagekit\Component\Routing\UrlAliasManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class UrlAliasListener implements EventSubscriberInterface
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var UrlAliasManager
     */
    protected $aliases;

    /**
     * Constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router  = $router;
        $this->aliases = $router->getUrlAliases();
    }

    /**
     * Handles alias mapping.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        try {

            if ($params = $this->aliases->matchRequest($request)) {
                if (isset($params['_route'])) {
                    if ($route = $this->router->getRoute($params['_route'])) {

                        $params['_controller'] = $route->getDefault('_controller');

                        $request->attributes->add($params);
                        unset($params['_route']);
                        unset($params['_controller']);
                        $request->attributes->set('_route_params', $params);
                    }
                }
            }

        } catch (\Exception $e) {}
    }

    /**
     * Handles alias mapping.
     *
     * @param GenerateRouteEvent $event
     */
    public function onGenerateRoute(GenerateRouteEvent $event)
    {
        $link = $event->getLink();

        if ($url = $this->aliases->generate($link->getLink(), array_diff_key($link->getParameters(), $link->getPathParameters()), $event->getReferenceType())
            or $url = $this->aliases->generate($link->getName(), $link->getParameters(), $event->getReferenceType())) {

            $event->setUrl($url);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => array('onKernelRequest', 40),
            'route.generate' => 'onGenerateRoute'
        );
    }
}
