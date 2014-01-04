<?php

/**
 * Bootstrap Loep library for OAuth 2 authentication
 */
$googlePlusConfig = $app['config']['googlePlus'];

$clientId = $googlePlusConfig['client_id'];
$clientSecret = $googlePlusConfig['client_secret'];
$redirectUri = $googlePlusConfig['redirect_uri'];
$config = array(
    'clientId'=>$clientId,
    'clientSecret'=>$clientSecret,
    'redirectUri'=>$redirectUri
);

$provider = new \OAuth2\Client\Provider\Google($config);

$app['oauth2.loep.client'] = $provider;