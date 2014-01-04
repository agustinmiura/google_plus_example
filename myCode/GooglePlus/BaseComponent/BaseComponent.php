<?php

namespace GooglePlus\BaseComponent;

use \Silex\Application as Application;

abstract class BaseComponent
{
    protected $application;

    public function __construct(Application $application)
    {
        if ( !(self::isValid($application)) ) {
            $message = 'Invalid parameter for the Default';
            $message .= ' redirection handler ';
            throw new \RuntimeException($message);
        }
        $this->application = $application;
    }

    public function getApplication()
    {
        return ($this->application);
    }

    public static function isValid(Application $application)
    {
        return ($application instanceof Application);
    }
}