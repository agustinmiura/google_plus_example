<?php

namespace GooglePlus\RedirectionEndPoint;

use GooglePlus\RedirectionEndPoint\IRedirectionHandler as IRedirectionHandler;
use Symfony\Component\HttpFoundation\Request as Request;
use Silex\Application as Application;

class DefaultHandler implements IRedirectionHandler
{
    private $application;

    public function __construct(Application $application)
    {
        if ( !(self::isValid($application)) ) {
            $message = 'Invalid parameter for the Default';
            $message .= ' redirection handler ';
            throw new \RuntimeException($message);
        }
        $this->application = $application;
    }


    public function handle(Request $request)
    {
        $app = $this->application;
        $oauth2Logger = \Logger::getLogger('OAuth2');
    
        $request = $app['request'];
        $session = $app['session'];
    
        $state = $request->get('state', '');
        $code = $request->get('code', -1);
        $error = $request->get('error', '');

        $oauth2Logger->debug(' Received in /oauth2callback the code :'.$code);

        $sameState = strcasecmp($state, $session->get('state', 'invalid'))==0;
        if (!$sameState) {
            //throw new \RuntimeException('The antiforgery tokens are not the same');
            $app['monolog']->addDebug('The antiforgery token is not the same');
        }

        $userDeny = (strcasecmp($error, '')!=0);
        
        $userAllowed = 'Yes';
        if ($userDeny) {
            $userAllowed = 'No';
        }  
        $googlePlusParams = $app['config']['googlePlus'];

        $params = array(
            'code'=>$code,
            'client_id'=>$googlePlusParams['client_id'],
            'client_secret'=>$googlePlusParams['client_secret'],
            'redirect_uri'=>$googlePlusParams['redirect_uri'],
            'grant_type'=>'authorization_code'
        );
        $accessTokenGetter = new \GooglePlus\AccessTokenGetter();
        $rawToken = $accessTokenGetter->getToken($params);
        $tokenInfo = json_decode($rawToken);

        $oauth2Logger->debug(' Receive from OAuth2Token end point the info :'.$rawToken);

        $isValid = (isset($tokenInfo->access_token) 
            && isset($tokenInfo->token_type) 
            && isset($tokenInfo->expires_in) 
            && isset($tokenInfo->id_token));

        if (!$isValid) {
            throw new \RuntimeException('Cannot access to the token info');
        }
        
        if ($isValid) {
            $app['session']->set('token', $tokenInfo);
            $token = $tokenInfo->access_token;

            $googlePlusHelper = new \GooglePlus\GooglePlusHelper();
            $idInformation = $googlePlusHelper->getUserInfo($token);
            $profileInformation = $googlePlusHelper->getProfileInformation($token);

            $id = $idInformation->id;
            $email = $idInformation->email;
            $displayName = $profileInformation->displayName;

            $app['session']->set('id', $id);
            $app['session']->set('idInformation', $idInformation);
            $app['session']->set('profileInformation', $profileInformation);
        }

        return $app['twig']->render('oauth2callback.html', array(
            'userAllowed'=>'Yes',
            'userId'=>$id,
            'displayName'=>$displayName,
            'email'=>$email
        ));

    }

    public static function isValid(Application $application)
    {
        return ($application instanceof Application);
    }
}
