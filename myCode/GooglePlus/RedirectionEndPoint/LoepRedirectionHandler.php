<?php

namespace GooglePlus\RedirectionEndPoint;

use GooglePlus\RedirectionEndPoint\IRedirectionHandler as IRedirectionHandler;
use Symfony\Component\HttpFoundation\Request as Request;
use Silex\Application as Application;
use \GooglePlus\BaseComponent\BaseComponent as BaseComponent;

class LoepRedirectionHandler extends BaseComponent implements IRedirectionHandler
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

        /**
         * Request the access token 
         * to the user token information
         * is an object with the 
         * class \OAuth2\Client\Token\AccessToken
         */
        $client = $app['oauth2.loep.client'];
        $tokenInformation = $client->getAccessToken(
            'authorization_code', 
            array('code'=>$code)
        );
        $accessToken = $tokenInformation->accessToken;
        $expires = $tokenInformation->expires;
        $refreshToken = $tokenInformation->refreshToken;
        $uid = $tokenInformation->uid;
        /**
         *  Get the user details
         *  Receive an object with class 
         *  \OAuth2\Client\Provider\User 
         */
        $userDetails = $client->getUserDetails($tokenInformation);
        $uid = $userDetails->uid;
        $nickname = $userDetails->nickname;
        $name = $userDetails->name;
        $firstName = $userDetails->firstName;
        $lastName = $userDetails->lastName;
        $email = $userDetails->email;
        $location = $userDetails->location;
        $description = $userDetails->description;
        $imageUrl = $userDetails->imageUrl;
        $urls = $userDetails->urls; 

        $displayName = $firstName.' '.$lastName;

        $isValid = (isset($userDetails->email)) 
            && (isset($userDetails->email));

        if (!$isValid) {
            throw new \RuntimeException('Cannot access to the token info');
        }

        $tokenInfo = new \StdClass();
        $tokenInfo->access_token = $accessToken;
        $tokenInfo->expires_in = $expires;
        $tokenInfo->id_token = $uid;
        $id = $uid;
        $tokenInfo->token_type = 'Bearer';

        $profileInformation = new \StdClass();
        $paramsToSet = array(
            'king'=>'',
            'etag'=>'',
            'gender'=>'',
            'objectType'=>'person',
            'id'=>$id,
            'displayName'=>$displayName,
            'name'=>array(
                'familyName'=>'',
                'givenName'=>''
            ),
            'url'=>'',
            'image'=>array(
                'url'=>''
            ),
            'isPlusUser'=>true,
            'language'=>'en',
            'ageRange'=>array(
                'min'=>12
            ),
            'verified'=>false
        );
        foreach($paramsToSet as $key=>$value) 
        {
            $profileInformation->$key = $value;
        }
        
        $app['session']->set('token', $tokenInfo);
        $app['session']->set('id', $id);
        $app['session']->set('idInformation', $idInformation);
        $app['session']->set('profileInformation', $profileInformation);

        return $app['twig']->render('oauth2callback.html', array(
            'userAllowed'=>'Yes',
            'userId'=>$id,
            'displayName'=>$displayName,
            'email'=>$email
        ));

    }
}
