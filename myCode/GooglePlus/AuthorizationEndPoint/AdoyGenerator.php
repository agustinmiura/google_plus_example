<?php

namespace GooglePlus\AuthorizationEndPoint;

use \GooglePlus\AuthorizationEndPoint\IAuthorizationEndPointGenerator 
    as IGenerator; 

use \GooglePlus\BaseComponent\BaseComponent as BaseComponent;
use \Silex\Application as Application;

class AdoyGenerator extends BaseComponent implements IGenerator
{
    protected $authorizationEndPoint;
    protected $redirectUri;

    public function __construct(
        Application $application, $authorizationEndPoint, $redirectUri) {

        parent::__construct($application);
        $this->authorizationEndPoint = $authorizationEndPoint;
        $this->redirectUri = $redirectUri;

    }

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
        $client = $app['oauth2.adoy.client'];
        return $client->getAuthenticationUrl(
            $this->authorizationEndPoint, 
            $this->redirectUri
        );
    }


}