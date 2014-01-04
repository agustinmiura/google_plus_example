<?php

namespace GooglePlus\AuthorizationEndPoint;

use \InoOicClient\Flow\Basic as BasicFlow; 
use \GooglePlus\AuthorizationEndPoint\IAuthorizationEndPointGenerator 
    as IGenerator; 

class NovakovGenerator implements IGenerator
{
    /**
     * @var BasicFlow
     */
    private $flow;

    /**
     * OAuth2 scope for the request
     * @var [type]
     */
    private $scope;

    public function __construct(BasicFlow $flow, $scope)
    {
        if (!self::isValid($flow, $scope)) {
            $message = ' Invalid flow object for the Novakov generator or ';
            $message .= ' scope ';
            throw new \RuntimeException($message);
        }

        $this->flow = $flow;
        $this->scope = $scope;
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
        $scope = $this->scope;
        return ($this->flow->getAuthorizationRequestUri($scope));
    }

    public static function isValid(BasicFlow $flow, $scope) 
    {
        $answer = ($flow instanceof BasicFlow);
        return ($answer && is_string($scope) && strlen($scope)>=1);

    }
}