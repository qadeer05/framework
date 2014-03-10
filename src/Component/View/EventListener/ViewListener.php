<?php

namespace Pagekit\Component\View\EventListener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Pagekit\Component\Routing\Event\ConfigureRouteEvent;
use Pagekit\Component\View\Event\ActionEvent;
use Pagekit\Component\View\View;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ViewListener implements EventSubscriberInterface
{
    /**
     * @var View
     */
    protected $view;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * Constructor.
     *
     * @param View   $view
     * @param Reader $reader
     */
    public function __construct(View $view, Reader $reader = null)
    {
        $this->view = $view;
        $this->reader = $reader;
    }

    /**
     * Reads the @View annotations from the controller stores them in the "view" option.
     *
     * @param ConfigureRouteEvent $event
     */
    public function onConfigureRoute(ConfigureRouteEvent $event)
    {
        if (!$this->reader) {
            $this->reader = new SimpleAnnotationReader;
            $this->reader->addNamespace('Pagekit\Component\View\Annotation');
        }

        if ($annotation = $this->reader->getMethodAnnotation($event->getMethod(), 'Pagekit\Component\View\Annotation\View')) {
            $route = $event->getRoute();
            $route->setOption('view', $annotation->getTemplate());
            $route->setOption('view_layout', $annotation->getLayout());
        }
    }

    /**
     * Renders view layout.
     *
     * @param GetResponseForControllerResultEvent  $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $result  = $event->getControllerResult();

        if (null !== $template = $request->attributes->get('_route_options[view]', null, true) and (null === $result || is_array($result))) {
            $response = new Response($result = $this->view->render($template, $result ?: array()));
        }

        if (null !== $layout = $request->attributes->get('_route_options[view_layout]', null, true)) {
            $this->view->setLayout($layout);
        }

        if ($layout = $this->view->getLayout()) {
            $this->view->addAction('content', function (ActionEvent $e) use ($result) { $e->setContent((string) $result); });
            $response = new Response($this->view->render($layout));
        }

        if (isset($response)) {
            $event->setResponse($response);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'route.configure' => 'onConfigureRoute',
            'kernel.view'     => array('onKernelView', -5)
        );
    }
}
