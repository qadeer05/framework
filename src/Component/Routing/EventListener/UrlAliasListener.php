<?php

namespace Pagekit\Component\Routing\EventListener;

use Pagekit\Component\Routing\Event\GenerateUrlEvent;
use Pagekit\Component\Routing\UrlAliasManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UrlAliasListener implements EventSubscriberInterface
{
    /**
     * @var UrlAliasManager
     */
    protected $aliases;

    /**
     * Constructor.
     *
     * @param UrlAliasManager $aliases
     */
    public function __construct(UrlAliasManager $aliases)
    {
        $this->aliases = $aliases;
    }

    /**
     * Handles alias mapping.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($source = $this->aliases->source($request->getPathInfo())) {
            $request->attributes->set('_system_path', $source);
        }
    }

    /**
     * Handles alias mapping.
     *
     * @param GenerateUrlEvent $event
     */
    public function onGenerateUrl(GenerateUrlEvent $event)
    {
        if ($path = $this->aliases->alias($event->getPathInfo())) {
            $event->setPathInfo($path);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 48),
            'url.generate'        => 'onGenerateUrl'
        );
    }
}
