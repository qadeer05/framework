<?php

namespace Pagekit\Component\Mail;

use Swift_Attachment;
use Swift_Image;
use Swift_Message;

class Message implements MessageInterface
{
    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * @var Swift_Message
     */
    protected $message;

    /**
     * Create a new Message.
     *
     * @param MailerInterface $mailer
     * @param Swift_Message   $message
     */
    public function __construct(MailerInterface $mailer, Swift_Message $message)
    {
        $this->mailer  = $mailer;
        $this->message = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function generateId()
    {
        return $this->message->generateId();
    }

    /**
     * {@inheritdoc}
     */
    public function subject($subject)
    {
        $this->message->setSubject($subject);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->message->getSubject();
    }

    /**
     * {@inheritdoc}
     */
    public function date($date)
    {
        $this->message->setDate($date);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDate()
    {
        return $this->message->getDate();
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->message->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function body($body, $contentType = null, $charset = null)
    {
        $this->message->setBody((string) $body, $contentType, $charset);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function returnPath($address)
    {
        $this->message->setReturnPath($address);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReturnPath()
    {
        return $this->message->getReturnPath();
    }

    /**
     * {@inheritdoc}
     */
    public function sender($address, $name = null)
    {
        $this->message->setSender($address, $name);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSender()
    {
        return $this->message->getSender();
    }

    /**
     * {@inheritdoc}
     */
    public function from($addresses, $name = null)
    {
        $this->message->setFrom($addresses, $name);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFrom()
    {
        return $this->message->getFrom();
    }

    /**
     * {@inheritdoc}
     */
    public function replyTo($addresses, $name = null)
    {
        $this->message->setReplyTo($addresses, $name);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReplyTo()
    {
        return $this->message->getReplyTo();
    }

    /**
     * {@inheritdoc}
     */
    public function to($addresses, $name = null)
    {
        $this->message->setTo($addresses, $name);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTo()
    {
        return $this->message->getTo();
    }

    /**
     * {@inheritdoc}
     */
    public function cc($addresses, $name = null)
    {
        $this->message->setCc($addresses, $name);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCc()
    {
        return $this->message->getCc();
    }

    /**
     * {@inheritdoc}
     */
    public function bcc($addresses, $name = null)
    {
        $this->message->setBcc($addresses, $name);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBcc()
    {
        return $this->message->getBcc();
    }

    /**
     * {@inheritdoc}
     */
    public function attach($file, $name = null, $mime = null)
    {
		return $this->prepAttachment(Swift_Attachment::fromPath($file), $name, $mime);
    }

    /**
     * {@inheritdoc}
     */
    public function attachData($data, $name, $mime = null)
    {
        return $this->prepAttachment(Swift_Attachment::newInstance($data, $name), null, $mime);
    }

    /**
     * {@inheritdoc}
     */
    public function embed($file, $cid = null)
    {
        $attachment = Swift_Image::fromPath($file);

        if ($cid) {
            $attachment->setId(strpos($cid, 'cid:') === 0 ? $cid : 'cid:'.$cid);
        }

        return $this->message->embed($attachment);
    }

    /**
     * {@inheritdoc}
     */
    public function embedData($data, $name, $contentType = null)
    {
		return $this->message->embed(Swift_Image::newInstance($data, $name, $contentType));
    }

    /**
     * {@inheritdoc}
     */
    public function queue(&$errors = array())
    {
        return $this->mailer->queue($this->message, $errors);
    }

    /**
     * {@inheritdoc}
     */
    public function send(&$errors = array())
    {
        return $this->mailer->send($this->message, $errors);
    }

	/**
	 * Prepare and attach the given attachment.
	 *
	 * @param Swift_Attachment $attachment
	 * @param string $mime
     * @param string $name
	 * @return Message
	 */
	protected function prepAttachment(Swift_Attachment $attachment, $mime = null, $name = null)
	{
		if (null !== $mime) {
			$attachment->setContentType($mime);
		}

		if (null !== $name) {
			$attachment->setFilename($name);
		}

		$this->message->attach($attachment);

		return $this;
	}
}
