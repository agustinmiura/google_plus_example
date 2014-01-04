<?php
/**
 * This is a prototype that show how
 * to use the zend http client 
 * to make http request instead of doing 
 * raw curls request
 */
include_once(__DIR__.'/../include/bootstrap.php');
include_once(ROOT_PATH.'/include/app.php');

echo PHP_EOL.'Start the zend request test'.PHP_EOL;

$oauth2Logger = \Logger::getLogger('OAuth2');

$headers = array('Content-Type'=>'application/x-www-form-url-encoded');

$config = $app['config'];
$code = '4/XenVuWaIguzHOVWB-HqBbhK8Cpl6.4kVpkjuc84wQaDn_6y0ZQNj6Ne38gQI';
$parameters = array(
    'code'=>$code,
    'client_id'=>$config['googlePlus.client_id'],
    'client_secret'=>$config['googlePlus.client_secret'],
    'redirect_uri'=>$config['googlePlus.redirect_uri'],
    'grant_type'=>'authorization_code'
);

$method = 'POST';

//request access token
$zendClientFactory = new \InoOicClient\Http\ClientFactory();
$zendClient = $zendClientFactory->createHttpClient();

$zendHttpRequest = new \Zend\Http\Request();
$zendHttpRequest->setMethod(\Zend\Http\Request::METHOD_POST);
$zendHttpRequest->setUri('https://accounts.google.com/o/oauth2/token');
//set the headers
$zendHttpHeaders = $zendHttpRequest->getHeaders();
$zendHttpHeaders->addHeaderLine('Content-Type: application/x-www-form-urlencoded');
$zendHttpHeaders->addHeaderLine('User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.65 Safari/537.36');
$zendHttpHeaders->addHeaderLine('Accept: */*');

$post = $zendHttpRequest->getPost();
foreach ($parameters as $key => $value) {
    $post->set($key, $value);
}

$zendHttpResponse = $zendClient->send($zendHttpRequest);
//Answer
$code = $zendHttpResponse->getStatusCode();
$statusMessage = $zendHttpResponse->getReasonPhrase();
$body = $zendHttpResponse->getBody();

$oauth2Logger->debug('The code for the token response is :'.$code);
$oauth2Logger->debug('The status message is :'.$statusMessage);
$oauth2Logger->debug('The body is :'.$body);

var_dump($body);
/*
//create the dispatcher
$tokenDispatcher = new \InoOicClient\Oic\Token\Dispatcher($zendClient);

//create the token request
$tokenRequest = new \InoOicClient\Oic\Token\Request();
$tokenRequest->setClientInfo($app['oauth2.novakov.clientInfo']);
$tokenRequest->setCode($code);
$tokenRequest->setGrantType('authorization_request');

//create the http request
$httpRequestBuilder = $tokenDispatcher->getHttpRequestBuilder();
$httpRequest = ($httpRequestBuilder->buildHttpRequest($tokenRequest, null));

//response
$zendHttpResponse = $zendClient->send($httpRequest);
$code = $zendHttpResponse->getStatusCode();
$statusMessage = $zendHttpResponse->getReasonPhrase();
$body = $zendHttpResponse->getBody();

$oauth2Logger->debug('The code for the token response is :'.$code);
$oauth2Logger->debug('The status message is :'.$statusMessage);
$oauth2Logger->debug('The body is :'.$body);
*/

