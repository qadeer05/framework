<?php

namespace Pagekit\Component\Routing\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class StringToResponseListener implements EventSubscriberInterface
{
    /**
     * Handles string responses.
     *
     * @param GetResponseForControllerResultEvent  $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $response = $event->getControllerResult();

        if (!(null === $response || is_array($response) || $response instanceof Response || (is_object($response) && !method_exists($response, '__toString')))) {
            $event->setResponse(new Response((string) $response));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => array('onKernelView', -10),
        );
    }
}
