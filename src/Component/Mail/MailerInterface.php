<?php

namespace Pagekit\Component\Mail;

interface MailerInterface
{
    /**
     * Creates and returns a message object
     *
     * @return MessageInterface
     */
    public function create();

    /**
     * Send email based on message object
     *
     * @param mixed $message Message object
     * @param array $errors An array of failures by-reference
     * @return boolean
     */
    public function send($message, &$errors = array());

    /**
     * Queues email based on message object
     *
     * @param mixed $message Message object
     * @param array $errors An array of failures by-reference
     * @return boolean
     */
    public function queue($message, &$errors = array());
}
