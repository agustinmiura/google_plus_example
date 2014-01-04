<?php

namespace GooglePlus;

class GooglePlusHelper
{
    public function __construct()
    {

    }
    /**
     * Returns a stdObject 
     * If there has been an error returns 
     * a stdObject with the attributes "error" and
     * "error_description"
     *
     * If the answer is correct returns:
     * ["id"]=userid
     * ["email"]=User email .
     * 
     * @param  [type] $accessToken [description]
     * @return [type]              [description]
     */
    public function getUserInfo($accessToken)
    {
        $url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=%s';
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

        $invalid = (isset($decoded->error)) 
            && (isset($decoded->error_description));

        return $decoded;
    }

    /**
     * If there has been an error returns the stdObject
     * with the attributes ['error'] and ['error_description']
     *
     * A valid answer looks like:
     *
     * {
     *    "kind": "plus#person",
     *    "etag": "\"r6E4NfYOn5dpL2w8XGt_3gHVskk/XC-YVxgzmihI7tsQQ2JcClbPzqc\"",
     *    "gender": "male",
     *    "objectType": "person",
     *    "id": "106965367834259231263",
     *    "displayName": " ",
     *    "name": {
     *     "familyName": " ",
     *     "givenName": " "
     *    },
     *    "url": "https://plus.google.com/111111",
     *    "image": {
     *     "url": "Url to the image"
     *    },
     *    "isPlusUser": true/false,
     *    "language": "en",
     *    "ageRange": {
     *     "min": 12
     *    },
     *    "verified": false
     *   }
     * 
     * @param  [type] $accessToken [description]
     * @return [type]              [description]
     */
    public function getProfileInformation($accessToken) 
    {
        $url = 'https://www.googleapis.com/plus/v1/people/me?access_token=%s';
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

        $invalid = (isset($decoded->error)) 
            && (isset($decoded->error_description));

        return $decoded;
    }

}