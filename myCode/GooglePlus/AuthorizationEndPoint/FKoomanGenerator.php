<?php

namespace GooglePlus\AuthorizationEndPoint;

use \GooglePlus\AuthorizationEndPoint\IAuthorizationEndPointGenerator 
    as IGenerator; 

use \fkooman\OAuth\Client\Api as OAuth2Client;
use \fkooman\OAuth\Client\Context as Context;

class FKoomanGenerator implements IGenerator 
{
    private $client;
    private $context;

    public function __construct(OAuth2Client $client,Context $context)
    {
        if (!self::isValid($client, $context)) {
            $message = 'Invalid parameters for the Constructor of Fkooman generator';
            throw new \RuntimeException($message);
        }

        $this->context = $context;
        $this->client = $client;
    }

    public function get($antiForgeryToken, $loginHint='') 
    {
        $context = $this->context;
        return ($this->client->getAuthorizeUri($context));
    }

    public static function isValid(OAuth2Client $client,Context $context) 
    {
        $isValid = true;
        $isValid = $isValid && ($client instanceof OAuth2Client);
        return ($isValid && ($context instanceof Context));
    }
}