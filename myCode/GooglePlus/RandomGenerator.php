<?php

namespace GooglePlus;

use Symfony\Component\HttpFoundation\Session\SessionInterface as ISession;

class RandomGenerator
{
    public function generateString($size) 
    {
        $token = rand($size, $size);
        return md5($token);
    }

}