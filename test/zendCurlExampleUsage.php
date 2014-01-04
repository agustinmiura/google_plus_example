<?php
/**
 * This sample shows how to use the
 * zend curl helper to request
 * valid tokens to query the 
 * Google Plus Api
 */
include_once(__DIR__.'/../include/bootstrap.php');
include_once(ROOT_PATH.'/include/app.php');

$oauth2Logger = \Logger::getLogger('OAuth2');

$method = 'POST';

$uri = 'https://accounts.google.com/o/oauth2/token';

$headers = array(
    'Content-Type'=>'application/x-www-form-urlencoded',
    'User-Agent'=>'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.65 Safari/537.36',
    'Accept'=>'*/*'
);

$config = $app['config'];
$code = '4/d208ZF_nOvwTo1VMdTPWUiWbe2Xd.kumAsRIsB1scaDn_6y0ZQNjKxy3-gQI';
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

$responseInformation = \GooglePlus\Helper\ZendCurlHelper::parseResponse($zendResponse);

var_dump($responseInformation);