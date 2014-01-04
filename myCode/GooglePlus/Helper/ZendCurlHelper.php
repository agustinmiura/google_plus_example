<?php

namespace GooglePlus\Helper;

class ZendCurlHelper
{
    /**
     * Zend http client
     * @var \Zend\Http\Client
    
    /**
     * Zend Curl Helper to make http request
     * 
     * @param [type] $uri     [description]
     * @param [type] $method  [description]
     * @param [type] $headers [description]
     */
    public function __construct()
    {
        $this->client = new \Zend\Http\Request();
    }

    public static function parseResponse(\Zend\Http\Response $response)
    {
        $code = $response->getStatusCode();
        $body = $response->getBody();
        $statusMessage = $response->getReasonPhrase();

        return array(
            'code'=>$code,
            'body'=>$body,
            'statusMessage'=>$statusMessage
        );
    }

    /**
     * [makeRequest description]
     * @param  String $method  'GET'/'POST'
     * @param  String $uri     url
     * @param  Array  $headers Array where 
     *                         each key is the name , and the value
     * @return [type]          
     */
    /*
    public static function makeRequest($method, $uri, $headers, $parameters)
    {
        $logger = \Logger::getLogger('GooglePlus');
        $message = '(method=%s), (uri=%s), (headers=%s), (parameters=%s)';
        $message = sprintf($message, $method, $uri,print_r($headers, true)
            , print_r($parameters, true));
        $logger->debug($message);

        $client = new \Zend\Http\Client();
        $zendHttpRequest = new \Zend\Http\Request();

        $method = \Zend\Http\Request::METHOD_GET;
        
        $getRequest = (strcmp($method, 'POST')==0) ? FALSE : TRUE; 
        if ($getRequest) {
            $method = \Zend\Http\Request::METHOD_POST;
            $getRequest = false;
            $logger->debug('Set method POST for request');
        }

        //
        $boolString = ($getRequest) ? 'Request is GET' : 'Request is POST';
        $logger->debug($boolString);

        $zendHttpRequest->setMethod($method);

        $zendHttpRequest->setUri($uri);

        $httpHeaderRequest = $zendHttpRequest->getHeaders();
        $rawHeader = ''; 
        foreach ($headers as $name => $value) {
            $rawHeader = $name.': '.$value;
            $httpHeaderRequest->addHeaderLine($rawHeader);
            $logger->debug('Set the header :'.$rawHeader);
        }

        if (!$getRequest) {
            $post = $zendHttpRequest->getPost();
            foreach ($parameters as $key => $value) {
                $post->set($key, $value);

                $message = sprintf('Set (name,value) param : (%s, %s)', 
                    $key, $value);
                $logger->debug($message);
            }
        } else {
            $logger->debug('Parameters for POST request are not set');
        }

        return $client->send($zendHttpRequest);
        
    }
    */
    public static function makeRequest($method, $uri, $headers, $parameters)
    {
        $logger = \Logger::getLogger('GooglePlus');
        $message = '(method=%s), (uri=%s), (headers=%s), (parameters=%s)';
        $message = sprintf($message, $method, $uri,print_r($headers, true)
            , print_r($parameters, true));
        $logger->debug($message);
        
        $clientConfig = array(
            'adapter' => 'Zend\Http\Client\Adapter\Curl',
            'curloptions' => array(
                CURLOPT_FOLLOWLOCATION => TRUE,
                CURLOPT_SSL_VERIFYPEER => FALSE
            ),
        );
        $client = new \Zend\Http\Client($uri, $clientConfig);
        $zendHttpRequest = new \Zend\Http\Request();
        
        $getRequest = ($method=='GET');
        if ($getRequest) {
            $zendHttpRequest->setMethod(\Zend\Http\Request::METHOD_GET);
        } else {
            $zendHttpRequest->setMethod(\Zend\Http\Request::METHOD_POST);
        }

        $zendHttpRequest->setUri($uri);

        //set the headers
        $zendHttpHeaders = $zendHttpRequest->getHeaders();
        $rawHeader = ''; 
        foreach ($headers as $name => $value) {
            $rawHeader = $name.': '.$value;
            $zendHttpHeaders->addHeaderLine($rawHeader);
            $logger->debug('Set the header :'.$rawHeader);
        }

        if (!$getRequest) {
            $post = $zendHttpRequest->getPost();
            foreach ($parameters as $key => $value) {
                $post->set($key, $value);

                $message = sprintf('Set (name,value) param : (%s, %s)', 
                    $key, $value);
                $logger->debug($message);
            }
        } else {
            $logger->debug('Parameters for POST request are not set');
        }

        $answer = $client->send($zendHttpRequest);
        
        $message = ' From the request in CurlZendHelper ';
        $message .= ' :'.((string)($answer));

        return $answer;
    }
}


