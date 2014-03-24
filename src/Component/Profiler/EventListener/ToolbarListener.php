<?php

namespace Pagekit\Component\Profiler\EventListener;

use Pagekit\Component\Routing\Router;
use Pagekit\Component\Routing\UrlGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Profiler\Profiler;

class ToolbarListener implements EventSubscriberInterface
{
    /**
     * @var Profiler
     */
    protected $profiler;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @var Router
     */
    protected $router;

    /**
     * Constructor.
     *
     * @param Profiler     $profiler
     * @param UrlGenerator $url
     * @param Router       $router
     */
    public function __construct(Profiler $profiler, UrlGenerator $url, Router $router)
    {
        $this->profiler = $profiler;
        $this->url      = $url;
        $this->router   = $router;
    }

    public function onKernelRequest()
    {
        $profiler = $this->profiler;

        $this->router->get('_profiler/{token}', '_profiler', function($token) use ($profiler) {

            if (!$profile = $profiler->loadProfile($token)) {
                return new Response;
            }

            $viewpath = __DIR__.'/../views';

            ob_start();
            include $viewpath.'/toolbar.php';
            $content = ob_get_contents();
            ob_get_clean();

            return new Response($content);

        })->setOption('maintenance', true);
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();

        // do not capture redirects or modify XML HTTP Requests
        if ($request->isXmlHttpRequest()) {
            return;
        }

        if ($request->attributes->get('_disable_profiler_toolbar')
                || !$response->headers->has('X-Debug-Token')
                || $response->isRedirection()
                || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
                || 'html' !== $request->getRequestFormat()
                || $response->getStatusCode() == 404
        ) {
            return;
        }

        $this->injectToolbar($response);
    }

    /**
     * Injects the web debug toolbar into the given Response.
     *
     * @param Response $response A Response instance
     */
    protected function injectToolbar(Response $response)
    {
        if (function_exists('mb_stripos')) {
            $posrFunction   = 'mb_strripos';
            $substrFunction = 'mb_substr';
        } else {
            $posrFunction   = 'strripos';
            $substrFunction = 'substr';
        }

        $content  = $response->getContent();
        $token    = $response->headers->get('X-Debug-Token');
        $route    = $this->url->route('_profiler', compact('token'));
        $url      = $this->url->to(__DIR__.'/../assets');
        $markup[] = "<div id=\"profiler\" data-url=\"$url\" data-route=\"$route\" style=\"display: none\"></div>";
        $markup[] = "<script src=\"$url/js/profiler.js\"></script>";

        if (false !== $pos = $posrFunction($content, '</body>')) {
            $content = $substrFunction($content, 0, $pos).implode("\n", $markup).$substrFunction($content, $pos);
            $response->setContent($content);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 128),
            KernelEvents::RESPONSE => array('onKernelResponse', -128)
        );
    }
}
