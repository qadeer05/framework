<?php

namespace Pagekit\Component\View\Csrf\Provider;

class DefaultCsrfProvider implements CsrfProviderInterface
{
    /**
     * The session attribute name for the token.
     *
     * @var string
     */
    protected $name;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct($name = '_csrf')
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $algo = in_array('sha512', hash_algos()) ? 'sha512' : 'sha1';

        return hash($algo, $this->getSessionId().$this->getSessionToken());
    }

    /**
     * {@inheritdoc}
     */
    public function validate($token)
    {
        return $token === $this->generate();
    }

    /**
     * Returns the session id.
     *
     * @return string
     */
    protected function getSessionId()
    {
        if (!session_id()) {
            session_start();
        }

        return session_id();
    }

    /**
     * Returns the session token.
     *
     * @return string
     */
    protected function getSessionToken()
    {
        if (!isset($_SESSION[$this->name])) {
            $_SESSION[$this->name] = sha1(uniqid(rand(), true));
        }

        return $_SESSION[$this->name];
    }
}
