<?php

namespace Pagekit\Component\Markdown;

use Pagekit\Framework\Application;
use Pagekit\Framework\ServiceProviderInterface;
use Michelf\MarkdownExtra;

class MarkdownServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['markdown'] = function() {
            return new MarkdownParser(new MarkdownExtra);
        };
    }

    public function boot(Application $app)
    {
    }
}
