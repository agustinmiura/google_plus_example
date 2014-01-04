<?php

namespace GooglePlus\RedirectionEndPoint;

use Symfony\Component\HttpFoundation\Request as Request;
use Silex\Application as Application;
use GooglePlus\BaseComponent\BaseComponent as BaseComponent;

class NovakovRedirectionHandler extends BaseComponent implements IRedirectionHandler
{
    private function buildRequest()
    {
        $zendHttpRequest = new \Zend\Http\Request();

        $zendHttpRequest->setUri();
        $zendHttpRequest->setMethod('POST');

        return $zendHttpRequest;
    }

    private function getAccessToken($code)
    {
        $method = 'POST';
        $uri = 'https://accounts.google.com/o/oauth2/token';
        $headers = array(
            'Content-Type'=>'application/x-www-form-urlencoded',
            'User-Agent'=>'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.65 Safari/537.36',
            'Accept'=>'*/*'
        );
        $app = $this->getApplication();
        $config = $app['config'];
        $parameters = array(
            'code'=>$code,
            'client_id'=>$config['googlePlus.client_id'],
            'client_secret'=>$config['googlePlus.client_secret'],
            'redirect_uri'=>$config['googlePlus.redirect_uri'],
            'grant_type'=>'authorization_code'
        );
        $zendResponse = \GooglePlus\Helper\ZendCurlHelper::makeRequest(
            $method, 
            $uri, 
            $headers, 
            $parameters
        );
        $responseArray = \GooglePlus\Helper\ZendCurlHelper::parseResponse(
            $zendResponse
        );
        return $responseArray;
    }

    public function handle(Request $request)
    {
        //app , request and session
        $app = $this->getApplication();
        $oauth2Logger = \Logger::getLogger('OAuth2');
    
        $request = $app['request'];
        $session = $app['session'];
    
        $state = $request->get('state', '');
        $code = $request->get('code', -1);
        $error = $request->get('error', '');

        /* Code that fails to get the accessToken
        //request access token
        $zendClientFactory = new \InoOicClient\Http\ClientFactory();
        $zendClient = $zendClientFactory->createHttpClient();
        
        //create the dispatcher
        $tokenDispatcher = new \InoOicClient\Oic\Token\Dispatcher($zendClient);

        //create the token request
        $tokenRequest = new \InoOicClient\Oic\Token\Request();
        $tokenRequest->setClientInfo($app['oauth2.novakov.clientInfo']);
        $tokenRequest->setCode($code);
        $tokenRequest->setGrantType('authorization_request');

        //request the token 
        //InoOicClient\Oic\Token\Response
        $httpRequest = null;
        $httpRequestBuilder = $tokenDispatcher->getHttpRequestBuilder();
        $httpRequest = ($httpRequestBuilder->buildHttpRequest($tokenRequest, null));
        
        //@var \Zend\Http\Response
        $zendHttpResponse = $zendClient->send($httpRequest);
        $code = $zendHttpResponse->getStatusCode();
        $statusMessage = $zendHttpResponse->getReasonPhrase();
        $body = $zendHttpResponse->getBody();

        $oauth2Logger->debug('The code for the token response is :'.$code);
        $oauth2Logger->debug('The status message is :'.$statusMessage);
        $oauth2Logger->debug('The body is :'.$body);
        
        $tokenResponse = $tokenDispatcher->sendTokenRequest(
            $tokenRequest, 
            $httpRequest
        );
        */
        $responseInformation = $this->getAccessToken($code);
        $code = $responseInformation['code'];
        $body = $responseInformation['body'];
        $tokenInfo = json_decode($body);
    
        $isValid = (isset($tokenInfo->access_token) 
            && isset($tokenInfo->token_type) 
            && isset($tokenInfo->expires_in) 
            && isset($tokenInfo->id_token));

        if (!$isValid) {
            $tokenAnswerAsString = print_r($tokenInfo, true);
            $message = 'Cannot access token info with access code : "%s"';
            $message .= ' and token info as answer : %s';
            $message = sprintf($message , $code, $tokenAnswerAsString);
            throw new \RuntimeException($message);
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