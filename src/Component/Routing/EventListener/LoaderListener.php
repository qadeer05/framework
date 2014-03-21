<?php

namespace Pagekit\Component\Routing\EventListener;

use Pagekit\Component\Routing\Loader\CachedLoader;
use Pagekit\Component\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LoaderListener implements EventSubscriberInterface
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
     * Writes routes cache on kernel terminate.
     *
     * @param PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        if (($loader = $this->router->getLoader()) instanceof CachedLoader) {
            $loader->write();
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::TERMINATE => array('onKernelTerminate', -256)
        );
    }
}
