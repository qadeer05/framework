<?php

namespace Pagekit\Framework\Templating;

use Pagekit\Framework\Exception\ExceptionHandlerInterface;

class RazrExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception)
    {
        $file = $exception->getFile();

        if ($file && substr($file, -6) == '.cache') {
            if (preg_match('/^<\?php\s\/\*\s(.+?)\s\*\//i', file_get_contents($file), $matches)) {
                $file = new \ReflectionProperty($exception, 'file');
                $file->setAccessible(true);
                $file->setValue($exception, $matches[1]);
            }
        }
    }
}
