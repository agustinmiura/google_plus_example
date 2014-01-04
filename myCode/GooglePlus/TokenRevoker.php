<?php

namespace GooglePlus;

class TokenRevoker
{
    public function __construct()
    {

    }

    /**
     * 
     * Make a get request to  
     * https://accounts.google.com/o/oauth2/revoke?token=$TOKEN
     * to invalidate the token
     * 
     * @param  [type] $accessToken [description]
     * @return boolean TRUE/FALSE false if fails
     */
    public function revokeToken($accessToken)
    {
        $url = 'https://accounts.google.com/o/oauth2/revoke?token=%s';
        $url = sprintf($url, $accessToken);

        $headers = array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
        );

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_POST, false);

        $response = curl_exec($handle);
        $decoded = json_decode($response);

        $failed = (isset($decoded->error));

        if (!$failed) {
            return true;
        } else {
            return false;
        }
    }


}