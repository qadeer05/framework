<?php

namespace Pagekit\Component\Session\Csrf\EventListener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Pagekit\Component\Routing\Event\ConfigureRouteEvent;
use Pagekit\Component\Session\Csrf\Exception\BadTokenException;
use Pagekit\Component\Session\Csrf\Provider\CsrfProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class CsrfListener implements EventSubscriberInterface
{
    /**
     * @var CsrfProviderInterface
     */
    protected $provider;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * Constructor.
     *
     * @param CsrfProviderInterface $provider
     * @param Reader                $reader
     */
    public function __construct(CsrfProviderInterface $provider, Reader $reader = null)
    {
        $this->provider = $provider;
        $this->reader   = $reader;
    }

    /**
     * Reads the "@Token" annotations from the controller and stores them in the "_csrf" attribute.
     *
     * @param ConfigureRouteEvent $event
     */
    public function onConfigureRoute(ConfigureRouteEvent $event)
    {
        if (!$this->reader) {
            $this->reader = new SimpleAnnotationReader;
            $this->reader->addNamespace('Pagekit\Component\Session\Csrf\Annotation');
        }

        if ($annotation = $this->reader->getMethodAnnotation($event->getMethod(), 'Pagekit\Component\Session\Csrf\Annotation\Token')) {
            $event->getRoute()->setOption('_csrf_name', $annotation->getName());
        }
    }

    /**
     * Checks for the CSRF token and throws 401 exception if invalid.
     *
     * @param GetResponseEvent $event
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($name = $request->attributes->get('_route_options[_csrf_name]', false, true) and !$this->provider->validate($request->get($name))) {
            throw new BadTokenException(401, 'Invalid CSRF token.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'route.configure' => 'onConfigureRoute',
            'kernel.request'  => array('onKernelRequest', -10)
        );
    }
}
