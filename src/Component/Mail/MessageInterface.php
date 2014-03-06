<?php

namespace Pagekit\Component\Mail;

interface MessageInterface
{
    /**
     * Generates a valid Message-ID and switches to it.
     *
     * @return string
     */
    public function generateId();

    /**
     * Set the subject of the message.
     *
     * @param  string $subject
     * @return MessageInterface
     */
    public function subject($subject);

    /**
     * Get the subject of the message.
     *
     * @return string
     */
    public function getSubject();

    /**
     * Set the origination date of the message as a UNIX timestamp.
     *
     * @param  integer $date
     * @return MessageInterface
     */
    public function date($date);

    /**
     * Get the origination date of the message as a UNIX timestamp.
     *
     * @return int
     */
    public function getDate();

    /**
     * Get the body content of this message as a string.
     *
     * @return string|null
     */
    public function getBody();

    /**
     * Set the body of this entity as a string.
     *
     * @param  string $body
     * @param  string $contentType
     * @return MessageInterface
     */
    public function body($body, $contentType = null);

    /**
     * Set the return-path (bounce-detect) address.
     *
     * @param  string $address
     * @return MessageInterface
     */
    public function returnPath($address);

    /**
     * Get the return-path (bounce-detect) address.
     *
     * @return string
     */
    public function getReturnPath();

    /**
     * Set the sender of this message.
     *
     * @param  mixed $address
     * @param  string $name optional
     * @return MessageInterface
     */
    public function sender($address, $name = null);

    /**
     * Get the sender address for this message.
     *
     * @return string
     */
    public function getSender();

    /**
     * Set the From address of this message.
     *
     * @param  mixed  $addresses
     * @param  string $name
     * @return MessageInterface
     */
    public function from($addresses, $name = null);

    /**
     * Get the From address(es) of this message.
     *
     * @return string[]
     */
    public function getFrom();

    /**
     * Set the Reply-To address(es).
     *
     * @param  mixed  $addresses
     * @param  string $name
     * @return MessageInterface
     */
    public function replyTo($addresses, $name = null);

    /**
     * Get the Reply-To addresses for this message.
     *
     * @return string[]
     */
    public function getReplyTo();

    /**
     * Set the To address(es).
     *
     * @param  mixed  $addresses
     * @param  string $name
     * @return MessageInterface
     */
    public function to($addresses, $name = null);

    /**
     * Get the To addresses for this message.
     *
     * @return string[]
     */
    public function getTo();

    /**
     * Set the Cc address(es).
     *
     * @param  mixed  $addresses
     * @param  string $name
     * @return MessageInterface
     */
    public function cc($addresses, $name = null);

    /**
     * Get the Cc addresses for this message.
     *
     * @return string[]
     */
    public function getCc();

    /**
     * Set the Bcc address(es).
     *
     * @param  mixed  $addresses
     * @param  string $name
     * @return MessageInterface
     */
    public function bcc($addresses, $name = null);

    /**
     * Get the Bcc addresses for this message.
     *
     * @return string[]
     */
    public function getBcc();

	/**
	 * Attach a file to the message.
	 *
	 * @param  string $file
	 * @param  string $name
	 * @param  string $mime
	 * @return MessageInterface
	 */
    public function attach($file, $name = null, $mime = null);

	/**
	 * Attach in-memory data as an attachment.
	 *
	 * @param  string $data
	 * @param  string $name
	 * @param  string $mime
	 * @return MessageInterface
	 */
    public function attachData($data, $name, $mime = null);

    /**
     * Embed a file in the message and get the CID.
     *
     * @param  string $file
     * @param  string $cid
     * @return string
     */
    public function embed($file, $cid = null);

	/**
	 * Embed in-memory data in the message and get the CID.
	 *
	 * @param  string $data
	 * @param  string $name
	 * @param  string $contentType
	 * @return string
	 */
    public function embedData($data, $name, $contentType = null);

    /**
     * Queues the message for later sending.
     *
     * @param  array $errors
     * @return boolean
     */
    public function queue(&$errors = array());

    /**
     * Sends the message.
     *
     * @param  array $errors
     * @return boolean
     */
    public function send(&$errors = array());
}
