<?php

namespace Pagekit\Component\Mail\Tests;

class AnyObject
{
	public $methods = array();

    public function __set($name , $value)
    {

    }
    public function __get($name)
    {

    }
    public function __isset($name)
    {

    }
    public function __unset($name)
    {

    }
    public function __call($name, $arguments)
    {
    	return isset($this->methods[$name]) ? call_user_func_array($this->methods[$name], $arguments) : null;
    }
    public static function __callStatic($name, $arguments)
    {
        
    }
}
