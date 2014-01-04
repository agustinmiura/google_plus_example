<?php

namespace GooglePlus;

/**
 * Class to reques the access token 
 * after you get the authorization code
 */
class AccessTokenGetter
{
    public function __construct() 
    {

    }

    /**
     * [getToken description]
     * @param  [type] $params [description]
     *                $params['code']
     *                $params['client_id']
     *                $params['client_secret']
     *                $params['redirect_uri']
     *                $params['grant_type']
     * 
     * @return A String that contains a json element
     *         If the json object is invalid then contains the 
     *         error element inside
     */
    public function getToken($params)
    {
        if (!self::validParams($params)) {
            throw new \RuntimeException('Invalid parameter for the method get token');
        }

        $url = 'https://accounts.google.com/o/oauth2/token';

        $values = 'code=%s&client_id=%s&client_secret=%s&redirect_uri=%s&grant_type=%s';
        $values = sprintf($values, $params['code'], $params['client_id'], 
            $params['client_secret'],  $params['redirect_uri'], 
        $params['grant_type']);

        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 

        return curl_exec($ch);
    }

    public static function validParams($params) 
    {
        $valid = true;

        $isArray = is_array($params);

        if ($isArray) {
            $toCheck = array('code', 'client_id', 'client_secret', 'redirect_uri', 
            'grant_type');
            
            foreach ($toCheck as $name) {
                if (!isset($params[$name])) {
                    $valid = false;
                    break;
                }
            }
        } else {
            $valid = false;
        }
        return $valid;
    }

}