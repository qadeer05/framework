<?php

namespace Pagekit\Framework\Exception;

interface ExceptionHandlerInterface
{
    /**
     * Returns true if the given Exception was handled, false otherwise.
     *
     * @param  \Exception $exception
     * @return bool
     */
    public function handle(\Exception $exception);
}
