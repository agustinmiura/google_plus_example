<?php

namespace GooglePlus;

use Symfony\Component\HttpFoundation\Session\SessionInterface as ISession;

/**
 * @todo changeThis
 */
class SecurityManager
{
    public function isLoggedIn(ISession $session)
    {
        $id = $session->get('id', '-1');
        $matches = (preg_match('/^\d{4,}$/', $id));
        return ($matches);
    }

    public function signIn($userName, ISession $session)
    {
        $session->set('username', $username);
    }

    public function getCurrentUser()
    {
        return ($session->get('username', null));
    }

}