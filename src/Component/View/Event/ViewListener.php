<?php

namespace Pagekit\Component\View\Event;

use Pagekit\Component\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ViewListener implements EventSubscriberInterface
{
    /**
     * @var ViewInterface
     */
    protected $view;

    /**
     * Constructor.
     *
     * @param ViewInterface $view
     */
    public function __construct(ViewInterface $view)
    {
        $this->view = $view;
    }

    /**
     * Renders view layout.
     *
     * @param GetResponseForControllerResultEvent $event
     * @param string                              $name
     * @param EventDispatcherInterface            $dispatcher
     */
    public function onKernelView(GetResponseForControllerResultEvent $event, $name, $dispatcher)
    {
        $request = $event->getRequest();
        $result  = $event->getControllerResult();

        if (null !== $template = $request->attributes->get('_response[value]', null, true) and (null === $result || is_array($result))) {
            $response = $result = $this->view->render($template, $result ?: array());
        }

        if (null !== $layout = $request->attributes->get('_response[layout]', null, true)) {
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
            'kernel.view' => array('onKernelView', -5)
        );
    }
}
