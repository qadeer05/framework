<?php

namespace Pagekit\Component\View;

use Pagekit\Component\View\Event\ActionEvent;
use Pagekit\Component\View\EventListener\ViewListener;
use Pagekit\Framework\Application;
use Pagekit\Framework\ServiceProviderInterface;

class ViewServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['view'] = function($app) {

            $view = new View($app['events']);
            $view->set('app', $app);

            return $view;
        };
    }

    public function boot(Application $app)
    {
        $app['events']->addSubscriber(new ViewListener($view = $app['view']));

        $view->addAction('head', function(ActionEvent $event) use ($view) {

            $result = array();

            if ($title = $view->get('head.title')) {
                $result[] = sprintf('        <title>%s</title>', $title);
            }

            if ($links = $view->get('head.link', array())) {
                foreach($links as $rel => $attributes) {

                    if (!$attributes) {
                        continue;
                    }

                    $html = '';
                    foreach ($attributes as $name => $value) {
                        $html .= sprintf(' %s="%s"', $name, htmlspecialchars($value));
                    }
                    $result[] = sprintf('        <link rel="%s"%s/>', $rel, $html);
                }
            }

            $event->append(implode(PHP_EOL, $result));

        }, 4);
    }
}
