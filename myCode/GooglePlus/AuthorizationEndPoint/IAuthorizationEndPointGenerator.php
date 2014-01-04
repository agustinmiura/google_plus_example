<?php

namespace GooglePlus\AuthorizationEndPoint;

interface IAuthorizationEndPointGenerator
{
    /**
     * 
     * @param  String $antiForgeryToken Random string to send to the Oauth2 
     *                                         authorization end point .
     *                                         To avoid request from scripts
     *                                         or bots.
     *                                         The param is returned to the
     *                                         redirection end poing
     *
     * @param  String $loginHint Login hint for the Google Plus
     *
     * @return String
     */
    public function get($antiForgeryToken, $loginHint='');
}