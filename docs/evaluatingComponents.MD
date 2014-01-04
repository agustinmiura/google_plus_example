Evaluating OAuth2 components:
-----------------------------

Libraries tested:
-----------------

1.  https://github.com/fkooman/php-oauth-client Koofman library
2. 

Koofman library
---------------
Good code.
Bad documentation to use the logging.
Bad documentation to use the db storages.
Easy documentation to bootstrap.
Problems .Why i have to generate a unique client id
Functions.
Time to get up the implementation running : 12 hs + 6 hs .

 ivan-novakov/php-openid-connect-client
---------------------------------------


Time spent: 6hs + 8hs .

Source code : "https://github.com/ivan-novakov/php-openid-connect-client"

Simple documentation.
No logging.
Easy to bootstrap.
Mala documentación tuve que inspeccionar el código para que funcione
la librería. 
No se puede usar porque :
The library cannot be recommended for production use yet though. It hasn't been tested enough. There are some important features from the OpenID Connect specs missing - ID token validation, tools for discovery and registration etc. Anyway, I'm planning to add them in the future releases.
No lo recomienda el autor.

Test the zend library for connection
No funcionó , tuve que hacer el componente yo con ZendHttpClient.

Evaluating component http://freecode.com/projects/php-oauth-api
---------------------------------------------------------------

The freeCode api depends on the libraries:
```
http://freecode.com/projects/httpclass
```
Esta librería no se puede usar porque usa pasaje por referencia que 
en php 5.4 esta prohibido y en 5.3 deprecado .

Evaluating component from https://github.com/adoy/PHP-OAuth2/
---------------------------------------------------------------
Adoy library .
Uses composer : yes.
Esta incompleta solo contempla obtener la url de autenticación
y el token de acceso .
A pesar de tener composer el proceso para incluirla exige modificar el 
include path lo cual es bastante malo.
El tutorial para usarla es incompleto y esta mal redactado .
No posee componentes para invalidar el token.
No posee componentes para buscar la info del usuario .

Loep https://github.com/php-loep/oauth2-client
----------------------------------------------

Clients to test
---------------

 * https://github.com/php-loep/oauth2-client
 * https://github.com/silvester/ReverseOAuth2 ->Try this one here.
 * https://github.com/madewithlove/laravel-oauth2 ->Check this one.
 * https://github.com/hwi/HWIOAuthBundle ->Symfony bridge. -> USE THIS ONE . Demasiado completo
 * https://github.com/alchemy-fr/oauth2-php/blob/master/lib/OAuth2Client.php

 * https://github.com/php-loep/oauth2-client 639

 * Use this one .

Loep https://github.com/php-loep/oauth2-client
----------------------------------------------

 * La clase abstracta oculta muchos detalles . No es flexible.
 * Hace mucho que no se actualiza el código .
 * El tutorial es malo.
 * No provee métodos para revocar el access token. 
 * No provee métodos para generar la url de autenticación.
 * Provee un método que genera la url y redirige automáticamente.
 * No provee logging para saber que pasa .
 * Usa objectos con atributos públicos y viola el encapsulamiento .