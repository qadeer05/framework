<?php

namespace Pagekit\Framework\Exception;

use Symfony\Component\Debug\ExceptionHandler as BaseExceptionHandler;

class ExceptionHandler extends BaseExceptionHandler
{
    /**
     * @var array
     */
    protected $handlers = array();

    /**
     * Handles the given Exception.
     *
     * @param \Exception $exception
     */
    public function handle(\Exception $exception)
    {
        while (ob_get_level()) {
            ob_get_clean();
        }

        for ($i = count($this->handlers) - 1; $i >= 0; $i--) {
            if ($this->handlers[$i]->handle($exception)) {
                exit;
            }
        }

        parent::handle($exception);
    }

    /**
     * Pushes a handler to the end of the stack.
     */
    public function pushHandler(ExceptionHandlerInterface $handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * Removes the last handler in the stack and returns it.
     */
    public function popHandler()
    {
        return array_pop($this->handlers);
    }
}
