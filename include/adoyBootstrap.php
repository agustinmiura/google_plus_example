<?php
/**
 * Adoy bootstrap to start the library
 */
$oauth2Config = $app['config']['googlePlus'];
$clientId = $oauth2Config['client_id'];
$clientSecret = $oauth2Config['client_secret'];

$redirectUri = $oauth2Config['redirect_uri'];
$authorizationEndPoint = $oauth2Config['auth_uri']; 
$tokenEndPoint = $oauth2Config['token_uri'];

$adoyClient = new \OAuth2\Client($clientId, $clientSecret);

$app['oauth2.adoy.client'] = $adoyClient;

$authorizationUrl = $app['oauth2.adoy.client']->getAuthenticationUrl(
    $authorizationEndPoint, 
    $redirectUri
);

