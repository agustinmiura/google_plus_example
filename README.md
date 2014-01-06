Google oauth 2 sample
---------------------
Installation instructions
-------------------------
1.  Copy the file parameters.ini.sample to parameters.ini in the same folder
2.  Edit the values of the parameters.ini with the values of your application. For more instructions read below.
3.  Add to your /etc/hosts file the content from the file located in /deploy/etc.sample . So the request to the web page www.devbox.example2.com are served by your server.
4. Edit the php.ini used by your server like the one in /deploy/php.ini.sample to display the errors in the server.
5. Add to your lighttpd.conf file the virtual host configuration specified in vhost.conf.sample so all the request to the site www.devbox.example2.com are served by the webapplication.

Registering your application to use google OAuth2
-------------------------------------------------
-------------------------------------------------
*  Go to the url [Google code api console](https://code.google.com/apis/console).
* In the tab API Access add a new application and use the following parameters:
```
Redirect URIs:
    http://www.devbox.example2.com/oauth2callback
Javascript origins:
    http://www.devbox.example2.com/oauth2callback
```
* Go to the services tab
 and enable the option Google+Api.

Setting the parameters.ini
------------------------------------------------
------------------------------------------------
In the parameters.ini set the following values :
*  googlePlus.response_type="code"
```
We are requesting the auhtorization code to get the access token
to query the Google Plus Api
```
* googlePlus.client_id=""
```
Use here the application id given in the Google Plus Api console.
```
* googlePlus.redirect_uri="http://www.devbox.example2.com/oauth2callback"
```
In that url we send the authorization code to get the access token
```
* googlePlus.scope="https://www.googleapis.com/auth/userinfo.email"
```
We want to access the user profile information of the Google Plus profile
and the user email . If we use instead "https://www.googleapis.com/auth/plus.login" , we want to access only the profile information.
```
* googlePlus.access_type="online"
```
If we use "offline" we also want a refresh token to get a new token if the
current one has expired
```
* googlePlus.client_secret="Google application secret here"

Developer information
---------------------
Process for the webapplication to login

*   The user is redirected to the webpage of google to
    grant access to the application for the personal information.
*   Once the user has authorized the the authorization code is sent to the
url www.dev2.example.con/oauthcallback.
*   The web server makes a request with the following characteristics:
```
url:https://accounts.google.com/o/oauth2/token
headers:
    'Accept: application/json',
    'Content-Type: application/x-www-form-urlencoded'
body:'code=%s&client_id=%s&client_secret=%s&redirect_uri=%s&grant_type=%s'
With the correct values
```
And received the access token if the code is valid.
*   With the access token making a request to :
```
ttps://www.googleapis.com/oauth2/v2/userinfo?access_token=%s
```
Receivse the user id and the email.
To the url:
```
https://www.googleapis.com/plus/v1/people/me?access_token=%s
```
Gets the profile information.
```
Lessons learned:
----------------
*  The authorization code can be requested only one time . When you
have it you must save it in a place . If you continue requesting
more access codes those are invalid.
* The access token can be requested only one time and must be saved
in a safe place. If you request more than one time the access token 
with the authorization code you will receive an error.
* Once the user logouts from the application you must revoke the 
authorization code using the url 
```
https://accounts.google.com/o/oauth2/revoke?token=%s
```
* If request an access code and you lost it then you must reset
your application key and restart the process.

