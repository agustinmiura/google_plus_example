<?php

//Boostrap Fkooman component
/*Use the google object to hold the 
configuration for OAuth2
$path = ROOT_PATH.'/config/client_secret.json';
$encoded = file_get_contents($path);
$decoded = json_decode($encoded, true);
*/
$oauth2Config = $app['config']['googlePlus'];
$web = array(
    'client_id'=>$oauth2Config['client_id'],
    'client_secret'=>$oauth2Config['client_secret'],
    'redirect_uris'=>array($oauth2Config['redirect_uri']),
    'auth_uri'=>$oauth2Config['auth_uri'],
    'token_uri'=>$oauth2Config['token_uri']
);
$decoded = array(
    'web'=>$web
);

$clientConfig = new \fkooman\OAuth\Client\GoogleClientConfig($decoded);
$app['oauth2client.fkooman.clientConfig'] = $clientConfig;

//Add logging component
$monologLogger = new \Monolog\Logger('oauth2-logger');
$monologAdapter = new \Guzzle\Log\MonologLogAdapter($monologLogger);
$adapterFormat = \Guzzle\Log\MessageFormatter::DEBUG_FORMAT;

$path = $app['config']['fkooman.logFile'];
$monologLevel = \Monolog\Logger::DEBUG;
$streamHandler = new \Monolog\Handler\StreamHandler($path);

$monologLogger->pushHandler($streamHandler);

$monologPlugin = new \Guzzle\Plugin\Log\LogPlugin($monologAdapter, $adapterFormat);

//@todo fix this and use another storage
//      to avoid the errors
$sessionStorage = new \fkooman\OAuth\Client\SessionStorage();
$httpClient = new \Guzzle\Http\Client();
$httpClient->addSubscriber($monologPlugin);
$app['oauth2client.fkooman.httpClient'] = $httpClient;

$clientApi = new \fkooman\OAuth\Client\Api("fooClientConfigId", $clientConfig
    , $sessionStorage, $httpClient);
$app['oauth2client.fkooman'] = $app->share(function() use ($app, $clientApi) {
    return $clientApi;
});

//User name for the application it does not hold relation
//with the user of the remote service 
$applicationUser = 'agustin.m1985@gmail.com';
$googleAuthConfig = $app['config']['googlePlus'];

$scope = new \fkooman\OAuth\Client\Scope($googleAuthConfig['scope']);
$context = new \fkooman\OAuth\Client\Context($applicationUser, $scope);

$app['oauth2client.fkooman.context'] = $app->share(function() use ($app, $context) {
    return $context;
}); 

