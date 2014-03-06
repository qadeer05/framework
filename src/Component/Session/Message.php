<?php

namespace Pagekit\Component\Session;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

class Message extends FlashBag
{
    /**
     * Detailed debug information
     */
    const DEBUG = 'debug';

    /**
     * Interesting events
     */
    const INFO = 'info';

    /**
     * Exceptional occurrences that are not errors
     */
    const WARNING = 'warning';

    /**
     * Runtime errors
     */
    const ERROR = 'error';

    /**
     * Success messages
     */
    const SUCCESS = 'success';

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $storageKey The key used to store messages in the session.
     */
    public function __construct($name = 'messages', $storageKey = '_pk_messages')
    {
        parent::__construct($storageKey);

        $this->setName($name);
    }

    public function debug($message)
    {
        $this->add(self::DEBUG, $message);
    }

    public function info($message)
    {
        $this->add(self::INFO, $message);
    }

    public function warning($message)
    {
        $this->add(self::WARNING, $message);
    }

    public function error($message)
    {
        $this->add(self::ERROR, $message);
    }

    public function success($message)
    {
        $this->add(self::SUCCESS, $message);
    }

    public static function levels()
    {
        return array(
            self::DEBUG,
            self::INFO,
            self::WARNING,
            self::ERROR,
            self::SUCCESS
        );
    }
}
