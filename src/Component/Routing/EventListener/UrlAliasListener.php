<?php

namespace Pagekit\Component\Routing\EventListener;

use Pagekit\Component\Routing\Event\GenerateUrlEvent;
use Pagekit\Component\Routing\UrlAliasManager;
use Pagekit\System\Event\SystemInitEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
     * @param SystemInitEvent $event
     */
    public function onInit(SystemInitEvent $event)
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
            'init'         => 'onInit',
            'url.generate' => 'onGenerateUrl'
        );
    }
}
