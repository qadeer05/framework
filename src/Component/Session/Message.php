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

    /**
     * Adds debug message
     *
     * @param string $message
     */
    public function debug($message)
    {
        $this->add(self::DEBUG, $message);
    }

    /**
     * Adds info message
     *
     * @param string $message
     */
    public function info($message)
    {
        $this->add(self::INFO, $message);
    }

    /**
     * Adds warning message
     *
     * @param string $message
     */
    public function warning($message)
    {
        $this->add(self::WARNING, $message);
    }

    /**
     * Adds error message
     *
     * @param string $message
     */
    public function error($message)
    {
        $this->add(self::ERROR, $message);
    }

    /**
     * Adds success message
     *
     * @param string $message
     */
    public function success($message)
    {
        $this->add(self::SUCCESS, $message);
    }

    /**
     * Checks if messages exist
     *
     * @return bool
     */
    public function hasMessages()
    {
        foreach ($this->levels() as $level) {
            if ($this->has($level)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets array of message levels
     *
     * @return array
     */
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
