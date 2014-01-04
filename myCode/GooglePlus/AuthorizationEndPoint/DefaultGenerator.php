<?php

namespace GooglePlus\AuthorizationEndPoint;

use \GooglePlus\AuthorizationEndPoint\IAuthorizationEndPointGenerator 
    as IGenerator; 

class DefaultGenerator implements IGenerator 
{
    private $config;

    /**
     * Array with the keys: 
     *         
     * @param array $config 
     *              $config["response_type"]
     *              $config["client_id"]
     *              $config["redirect_uri"]
     *              $config["scope"]
     *              $config["access_type"]
     *              $config["approval_prompt"]
     */
    public function __construct(array $config)
    {
        if (self::isValid($config)) {
            $message = 'Cannot initialize the DefaultGenerator with the array';
            $message .= ' '.print_r($config, true).' ';
            throw new \RuntimeException($message);
        }

        $this->config = $config;
    }       

    public function get($antiForgeryToken, $loginHint='') 
    {       
        $googlePlusParams = $this->config;

        $url = 'https://accounts.google.com/o/oauth2/auth?response_type=%s';
        $url .= '&client_id=%s';
        $url .= '&redirect_uri=%s';
        $url .= '&scope=%s';
        $url .= '&state=%s';
        $url .= '&access_type=%s';
        $url .= '&approval_prompt=%s';
        $url .= '&login_hint=%s';

        $url = sprintf(
        $url, 
        $googlePlusParams['response_type'], 
        $googlePlusParams['client_id'], 
        $googlePlusParams['redirect_uri'], 
        $googlePlusParams['scope'], 
        $antiForgeryToken,
        $googlePlusParams['access_type'], 
        $googlePlusParams['approval_prompt'], 
        $loginHint);

        return $url;
    }

    public static function isValid($config) 
    {
        $names = array('respose_type', 'client_id', 'redirect_uri', 'scope', 
        'access_type', 'approval_prompt');
    
        $answer = true;

        foreach ($names as $eachName) {
            if (!isset($config[$eachName])) {
                $answer = false;
                break;
            }
        }

        return $answer;
    }
}