<?php

namespace GooglePlus\TokenRevoker;

use \GooglePlus\TokenRevoker\ITokenRevoker as ITokenRevoker;
use GooglePlus\BaseComponent\BaseComponent as BaseComponent;

/**
 * Invalidate a Google OAuth2 access token
 */
class FKoomanTokenRevoker extends BaseComponent implements ITokenRevoker 
{
    /**
     * Revoke the token against the 
     * revoke endpoint and remove it
     * from the storage 
     * 
     * @param  [type] $accessToken [description]
     * @return [type]              [description]
     */
    public function revokeToken($accessToken)
    {
        $oauth2Logger = \Logger::getLogger('OAuth2');

        $app = $this->getApplication();
        $clientApi = $app['oauth2client.fkooman'];
        $httpClient = $app['oauth2client.fkooman.httpClient'];
        $context = $app['oauth2client.fkooman.context'];

        $url = 'https://accounts.google.com/o/oauth2/revoke?token=%s';
        $url = sprintf($url, $accessToken);

        /**
         * @todo check if the answer from Google 
         * is valid
         */
        $rawAnswer = $httpClient->get($url)->send();
        $answer = json_decode($rawAnswer->getBody());

        $message = 'The answer against the revoke endpoint is ';
        $asString = print_r($answer, true);
        $message .= ' '.$asString.' in FKoomanTokenRevoker ';

        $oauth2Logger->debug($message);

        $clientApi->deleteAccessToken($context);
        $clientApi->deleteRefreshToken($context);
    
        return true;
    }
}