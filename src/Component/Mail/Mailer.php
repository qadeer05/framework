<?php

namespace Pagekit\Component\Mail;

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Swift_SpoolTransport;
use Swift_TransportException;

class Mailer implements MailerInterface
{
    /**
     * The Swift Mailer instance.
     *
     * @var Swift_Mailer
     */
    protected $swift;

    /**
     * The Swift Spool Transport instance.
     *
     * @var Swift_SpoolTransport
     */
    protected $queue;

    /**
     * Create a new Mailer instance.
     *
     * @param Swift_Mailer $swift
     * @param Swift_SpoolTransport $queue
     */
    public function __construct(Swift_Mailer $swift, Swift_SpoolTransport $queue)
    {
        $this->swift = $swift;
        $this->queue = $queue;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return new Message($this, new Swift_Message);
    }

    /**
     * {@inheritdoc}
     */
    public function send($message, &$errors = array())
    {
        return $this->swift->send($message, $errors);
    }

    /**
     * {@inheritdoc}
     */
    public function queue($message, &$errors = array())
    {
        return $this->queue->send($message, $errors);
    }

    /**
     * Test smtp connection with given settings.
     *
     * @param  string  $host
     * @param  integer $port
     * @param  string  $username
     * @param  string  $password
     * @param  string  $encryption
     * @throws Swift_TransportException
     */
    public static function testSmtpConnection($host = "localhost", $port = 25, $username = "", $password = "", $encryption = null)
    {
        Swift_SmtpTransport::newInstance($host, $port)
            ->setUsername($username)
            ->setPassword($password)
            ->setEncryption($encryption)
            ->start();
    }
}
