Bugs 
----

#1 The novakov library fails


To perform a single token request we use the following
code:
```
$httpClientFactory = new Http\ClientFactory();
$httpClient = $httpClientFactory->createHttpClient();

$tokenDispatcher = new Token\Dispatcher($httpClient);

$tokenRequest = new Token\Request();
$tokenRequest->setClientInfo($clientInfo);
$tokenRequest->setCode($authorizationCode);
$tokenRequest->setGrantType('authorization_code');

$tokenResponse = $tokenDispatcher->sendTokenRequest($tokenRequest);
printf("Access token: %s<br>", $tokenResponse->getAccessToken());
```
But this fails because when the line :
```
$tokenResponse = $tokenDispatcher->sendTokenRequest($tokenRequest);
```
Executes the code :
```
 public function sendTokenRequest(Request $request, Http\Request $httpRequest = null)
    {
        try {
            $httpRequest = $this->getHttpRequestBuilder()->buildHttpRequest($request, $httpRequest);
        } catch (\Exception $e) {
            throw new HttpRequestBuilderException(sprintf("Invalid request: [%s] %s", get_class($e), $e->getMessage()));
        }
        
```
And the method 
```
    public function buildHttpRequest(Request $request, Http\Request $httpRequest = null)
    {
        if (null === $httpRequest) {
            $httpRequest = new Http\Request();
        }
        
        $clientInfo = $request->getClientInfo();
        if (! $clientInfo instanceof ClientInfo) {
            throw new Exception\InvalidRequestException('No client info in request');
        }
        
        $endpointUri = $clientInfo->getTokenEndpoint();
        
        $httpRequest->setUri($endpointUri);
```
Uses null as value in the $endpointUri .
```
```

#2 The request to get the access token is malformed
----------------------------------------------------

The library when is used alone to get the access token malforms the 
request to get the access_token

Algo simple como mandar el request para el access token no funciona
, es demasiado código y no sirve.

Tuve que hacer código yo .