<?php
$oauth2Config = $app['config']['googlePlus'];

$arrayInfo = array(
    'client_id'=>$oauth2Config['client_id'],
    'redirect_uri'=>$oauth2Config['redirect_uri'],
    'authorization_endpoint'=>'https://accounts.google.com/o/oauth2/auth',
    'token_endpoint' => 'https://accounts.google.com/o/oauth2/token',
    'user_info_endpoint' => 'https://www.googleapis.com/oauth2/v1/userinfo',
    'authentication_info' => array(
        'method' => 'client_secret_post',
        'params' => array(
            'client_secret' => $oauth2Config['client_secret']
        )
    )
);

$novakovConfig = array('client_info'=>$arrayInfo);
$app['oauth2.novakov.flow'] = new \InoOicClient\Flow\Basic($novakovConfig);
/**
 * Create the clientInfo object
 */
$clientInfo = new \InoOicClient\Client\ClientInfo($novakovConfig);
$clientInfo->fromArray($arrayInfo);

$app['oauth2.novakov.clientInfo'] = $clientInfo;
