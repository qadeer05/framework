<?php

namespace Pagekit\Component\Auth\Tests\Fixtures;

use Pagekit\Component\Auth\UserInterface;

class User implements UserInterface
{
    protected $id = '12345';

    protected $username = 'username';

    protected $password = 'password';

    protected $authenticated;

    protected $roles = ['key' => 'the user role'];
    
    public function getId() {
    	return $this->id;
    }

    public function getUsername() {
    	return $this->username;
    }

    public function getPassword() {
    	return $this->password;
    }

    public function isAuthenticated()
    {
        return $this->authenticated;
    }

    public function setAuthenticated()
    {
        $this->authenticated = true;;
    }

    public function getRoles()
    {
        return $this->roles;
    }
}