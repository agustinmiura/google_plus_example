<?php

namespace GooglePlus\RedirectionEndPoint;

use GooglePlus\RedirectionEndPoint\IRedirectionHandler as IRedirectionHandler;
use Symfony\Component\HttpFoundation\Request as Request;
use Silex\Application as Application;
use GooglePlus\BaseComponent\BaseComponent as BaseComponent;

class AdoyRedirectionHandler extends BaseComponent implements IRedirectionHandler
{
    public function handle(Request $request)
    {
        $app = $this->application;
        $oauth2Logger = \Logger::getLogger('OAuth2');

        $request = $app['request'];
        $session = $app['session'];
        $config = $app['config'];
        $googlePlusParams = $config['googlePlus'];

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

        if ($userDeny) {
            $userAllowed = 'No';
        }  

        $redirectUri = $googlePlusParams['redirect_uri'];
        /**
         * Request the access token to 
         * the user
         * @var [type]
         */
        $client = $app['oauth2.adoy.client'];
        $params = array(
            'code'=>$code,
            'redirect_uri'=>$redirectUri
        );

        /**
         * array with
         * ['result'] = Result from the answer
         * ['code'] = Code for the request
         * ['content_type'] = Content type of the answer
         */
        $response = $client->getAccessToken(
            $googlePlusParams['token_uri'],
            'authorization_code',
            $params
        );

        $result = $response['result'];
        $code = $response['code'];
        $contentType = $response['content_type'];

        $isValid = (isset($result['access_token'])) 
            && (isset($result['token_type'])) 
            && (isset($result['expires_in'])) 
            && (isset($result['id_token']));

        $accessToken = $result['access_token'];
        $tokenType = $result['token_type'];
        $expiresIn = $result['expires_in'];
        $idToken = $result['id_token'];

        $tokenInfo = new \StdClass();
        $tokenInfo->access_token = $accessToken;
        $tokenInfo->token_type = $tokenType;
        $tokenInfo->expires_in = $expiresIn;
        $tokenInfo->id_token = $idToken;

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
}