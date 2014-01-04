<?php

namespace GooglePlus\AuthorizationEndPoint;

use \GooglePlus\AuthorizationEndPoint\IAuthorizationEndPointGenerator 
    as IGenerator; 

use \GooglePlus\BaseComponent\BaseComponent as BaseComponent;
use \Silex\Application as Application;

class LoepGenerator extends BaseComponent implements IGenerator
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
    public function get($antiForgeryToken, $loginHint='')
    {
        $app = $this->getApplication();
        $client = $app['oauth2.loep.client'];
        return $client->authorize();
    }


}