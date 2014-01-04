<?php

namespace GooglePlus\RedirectionEndPoint;

use GooglePlus\RedirectionEndPoint\IRedirectionHandler as IRedirectionHandler;
use Symfony\Component\HttpFoundation\Request as Request;
use Silex\Application as Application;

class FKoomanRedirectionHandler implements IRedirectionHandler
{
    /**
     * Client id associated with the Fkooman
     * information not the one in OAuth2
     * 
     * @var [type]
     */
    private $clientId;

    /**
     * 
     * @var
     */
    private $app;

    public function __construct(\Silex\Application $app) 
    {
        if (!self::isValid($app)) {
            $message = 'Cannot use constructor for FKooman redirection';
            $message .= 'handler with app not being a Silex Application';
            throw new \RuntimeException($message);
        }
        $this->app = $app;
    }

    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    public function handle(Request $request)
    {
        $app = $this->app;

        /**
         * Use the FKooman handler for the callback
         */    
        $oauth2Logger = \Logger::getLogger('OAuth2');
        $clientConfig = $app['oauth2client.fkooman.clientConfig'];
        $sessionStorage = new \fkooman\OAuth\Client\SessionStorage();

        $cb = new \fkooman\OAuth\Client\Callback( 
        $this->clientId, 
        $clientConfig, 
        $sessionStorage, 
        $app['oauth2client.fkooman.httpClient']
        );
        /**
         * fkooman\OAuth\Client\AccessToken
         */
        $request = $app['request'];
        $state = $request->get('state', '-1');
        $code = $request->get('code', '-1');
        $error = $request->get('error', NULL);

        $validCode = (strcasecmp($code, '-1')!==0);

        if ($validCode) {

            $parameters = array(
                'state'=>$state,
                'code'=>$code,
            );
        } else {

            $parameters = array(
                'state'=>$state,
                'code'=>$code,
                'error'=>$error
            );
        }

        $accessTokenContainer = $cb->handleCallback($parameters);
        $accessToken = $accessTokenContainer->getAccessToken();
        $tokenType = $accessTokenContainer->getTokenType();
        $expiresIn = $accessTokenContainer->getExpiresIn();

        /**
         * Store the token info
         */
        $tokenInfo = new \stdClass();
        $tokenInfo->access_token = $accessToken;
        $tokenInfo->token_type = $tokenType;
        $tokenInfo->expires_in = $expiresIn;
        $tokenInfo->id_token = 'IdToken';

        $app['session']->set('token', $tokenInfo);

        /**
         * Get the http client and make the requests
         */
        $httpClient = $app['oauth2client.fkooman.httpClient'];
        $bearerAuth = new \fkooman\Guzzle\Plugin\BearerAuth\BearerAuth($accessToken);
        $httpClient->addSubscriber($bearerAuth);

        /**
         * Get user info
         */
        $url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=%s';
        $url = sprintf($url, $accessToken);
        $rawUserInfo = $httpClient->get($url)->send();
        $idInformation = json_decode($rawUserInfo->getBody());

        /**
         * Get profile info
         */
        $url = 'https://www.googleapis.com/plus/v1/people/me?access_token=%s';
        $url = sprintf($url, $accessToken);
        $rawProfileInfo = $httpClient->get($url)->send();
        $profileInformation = json_decode($rawProfileInfo->getBody());

        if (!isset($profileInformation->id)) {
            $message = 'Cannot get profile or user information from ';
            $message .= ' Google OAuth2 information endpoints ';
            $message .= ' and token is '.$accessToken;

            throw new \RuntimeException($message);
        }

        $id = $profileInformation->id;
        $email = $idInformation->email;
        $displayName = $profileInformation->displayName;

        $app['session']->set('id', $id);
        $app['session']->set('idInformation', $idInformation);
        $app['session']->set('profileInformation', $profileInformation);

        return $app->redirect('/main');
    }

    public static function isValid(\Silex\Application $app) 
    {
        return ($app instanceof \Silex\Application);
    }
}