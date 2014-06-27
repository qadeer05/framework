<?php

namespace Pagekit\Component\View\Event;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Pagekit\Component\Routing\Event\ConfigureRouteEvent;
use Pagekit\Component\View\View;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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

            if ($view = $annotation->getTemplate()) {
                $route->setDefault('_view', $view);
            }

            if (null !== $layout = $annotation->getLayout()) {
                $route->setDefault('_view_layout', $layout);
            }
        }
    }

    /**
     * Renders view layout.
     *
     * @param GetResponseForControllerResultEvent $event
     * @param string                              $name
     * @param EventDispatcherInterface            $dispatcher
     */
    public function onKernelView(GetResponseForControllerResultEvent $event, $name, EventDispatcherInterface $dispatcher)
    {
        $request = $event->getRequest();
        $result  = $event->getControllerResult();

        if (null !== $template = $request->attributes->get('_view') and (null === $result || is_array($result))) {
            $response = $result = $this->view->render($template, $result ?: array());
        }

        if (null !== $layout = $request->attributes->get('_view_layout')) {
            $this->view->setLayout($layout);
        }

        if ($layout = $this->view->getLayout()) {
            $this->view->addAction('content', function (ActionEvent $e) use ($result) { $e->setContent((string) $result); });
            $dispatcher->dispatch('view.layout', $e = new LayoutEvent($layout));
            $response = $this->view->render($e->getLayout(), $e->getParameters());
        }

        if (isset($response)) {
            $event->setResponse(new Response($response));
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
