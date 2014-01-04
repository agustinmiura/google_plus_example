<?php
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as NotFoundHttpException;

use GooglePlus\AccessTokenGetter as AccessTokenGetter;
use GooglePlus\GooglePlusHelper as GooglePlusHelper;
use GooglePlus\TokenRevoker as TokenRevoker;

$app->get('/', function() use ($app) {
    $session = $app['session'];
    $isLoggedIn = $app['security.manager']->isLoggedIn($session);

    $asString = ($isLoggedIn) ? 'TRUE' : 'FALSE';
    $app['monolog']->addDebug('The is logged in is :'.$asString);

    if ($isLoggedIn) {
        return $app->redirect('/main');
    } else {
        return $app->redirect('/to-login');
    }
});

$app->get('/oauth2callback', function() use ($app) {
    /**
     * Use default handler for the /oauth2callback
     */
    return $app['oauth2.redirectionEndPoint.handler']->handle($app['request']);
 
    //Use the FKooman handler for the callback
    /*
    $handler = $app['oauth2.redirectionEndPoint.handler'];
    $handler->setClientId('fooClientConfigId');
    */
    return $handler->handle($app['request']);
});

$app->get('/main', function() use ($app) {
    $session = $app['session'];
    $isLoggedIn = $app['security.manager']->isLoggedIn($session);

    if (!$isLoggedIn) {
        $app->redirect('/fail');
    }

    $id = $session->get('id', -1);

    $emptyObject =new \stdClass();
    $emptyObject->id = -1;
    $emptyObject->email = '';
    $emptyObject->displayName = '';

    $idInformation = $session->get('idInformation', $emptyObject);
    $profileInformation = $session->get('profileInformation', $emptyObject);

    /**
     * @todo remove
     */
    $asString = print_r($idInformation, true);
    $app['monolog']->addDebug('The id information is :'.$asString);
    $asString = print_r($profileInformation, true);
    $app['monolog']->addDebug('The profile information is '.$asString);
    /**
     * @todo remove
     */

    return $app['twig']->render('oauth2callback.html', array(
        'userAllowed'=>'Yes',
        'userId'=>$id,
        'displayName'=>$profileInformation->displayName,
        'email'=>$idInformation->email
    ));
});

$app->get('/logout', function() use ($app) {
    $oauthLogger = \Logger::getLogger('OAuth2');
  
    $session = $app['session'];
    
    if (!$app['security.manager']->isLoggedIn($session)) {
        return $app->redirect('/to-login');
    }

    $tokenInfo = $session->get('token', null);
    $accessToken = $tokenInfo->access_token;
    
    $tokenRevoker = new TokenRevoker();
    
    //use default token revoker
    $answer = $app['oauth2.token.revoker']->revokeToken($accessToken);

    //log the answer
    $boolString = $answer ? 'Invalidated' : 'Not invalidated';
    $oauthLogger->debug('The result from invalidating the token is'.$boolString);

    //clear the session
    /*
    $session->clear();
    $session->invalidate();
    */

    if ($answer) {
        $session->clear();
        $session->invalidate();

        /**
         * @todo remove
         */
        $asString = print_r($tokenInfo, true);
        $oauthLogger->debug(
            ' In the /logout invalidate the token :'.$asString
        );

        /**
         * @todo remove
         */
        $asString = print_r($tokenInfo, true);
        $oauthLogger->debug(
            'In the /logout i have invalidated the token : '.$asString
        );

        return $app->redirect('/invalidated');
    }
    return $app->redirect('/not-invalidated');
});

$app->get('/to-login', function() use ($app) {
    $request = $app['request'];
    $session = $app['session'];
    $randomGenerator = $app['service.random'];

    $token = $randomGenerator->generateString(64);
    $session->set('state', $token);

    $url = $app['oauth2.authorizationEndPoint.generator']->get($token, '');

    return $app->redirect($url);
});
