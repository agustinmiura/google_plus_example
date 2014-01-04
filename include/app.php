<?php
use Silex\Application;
use Monolog\Logger as MonologLogger;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\HttpFoundation\Request as Request;
use Silex\Provider\SessionServiceProvider as SessionServiceProvider;

$app = new \Silex\Application();

$app['debug'] = true;

$configPath = ROOT_PATH.'/config/parameters.ini';
$config = parse_ini_file($configPath); 

$config['googlePlus'] = array(
    'response_type'=>$config['googlePlus.response_type'],
    'client_id'=>$config['googlePlus.client_id'],
    'redirect_uri'=>$config['googlePlus.redirect_uri'],
    'scope'=>$config['googlePlus.scope'],
    'state'=>'11',
    'access_type'=>$config['googlePlus.access_type'],
    'approval_prompt'=>$config['googlePlus.approval_prompt'],
    'client_secret'=>$config['googlePlus.client_secret'],
    'auth_uri'=>$config['googlePlus.auth_uri'],
    'token_uri'=>$config['googlePlus.token_uri']
);

$app['config']=$config;

$app->register(new UrlGeneratorServiceProvider());
$app->register(new SessionServiceProvider());

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => $app['config']['monolog.logfile'],
    'monolog.level' => MonologLogger::DEBUG
));

/**
 * Register twig service
 */
$debugTwig = ($app["debug"]===true);
$optimizeTwig = ($app["debug"]===false);
$app->register(new TwigServiceProvider(), array(
    'twig.path' => array(ROOT_PATH.'/templates'),
    'twig.options' => array(
        'cache' => $app['config']['twig.cache'],
        'debug' => true,
        'strict_variables' => true,
        /*Autoreload template whenever the src changes set TRUE for development*/
        'auto_reload'=>true,
        //0 to disable , -1 all optimizations enabled
        'optimizations'=>0
    ),
));

$app['security.manager'] = $app->share(function() use ($app) {
    return new \GooglePlus\SecurityManager();
});

$app['service.random']= $app->share(function() use ($app) {
    return new \GooglePlus\RandomGenerator();
});

//Boostrap Fkooman component
include_once(ROOT_PATH.'/include/fkoomanBootstrap.php');
//Bootstrap Novakov component
include_once(ROOT_PATH.'/include/novakovBootstrap.php');
//Bootstrap AdoyClient
include_once(ROOT_PATH.'/include/adoyBootstrap.php');
//Bootstrap the loep Component
include_once(ROOT_PATH.'/include/loepBootstrap.php');

$app['oauth2.authorizationEndPoint.generator'] = $app->share(function() use ($app) {
    //Google plus default authorization generator
    /*
    return new \GooglePlus\AuthorizationEndPoint\DefaultGenerator(
        $app['config']['googlePlus']
    );
    */
    //FKoomanGenerator
    /*
    return new GooglePlus\AuthorizationEndPoint\FKoomanGenerator(
        $app['oauth2client.fkooman'], 
        $app['oauth2client.fkooman.context']
    );
    */
    //Generate the end point with the NovakovExtension
    /*
    $basicFlow = $app['oauth2.novakov.flow'];
    return new \GooglePlus\AuthorizationEndPoint\NovakovGenerator(
        $basicFlow, 
        $app['config']['googlePlus']['scope']
    );
    */
    //Generate end point with the Adoy extension
    /**
     * Instantiate the class 
     */
    //return new \GooglePlus\AuthorizationEndPoint\AdoyGenerator(
    //$app, 
    //$autorizationEndPoint, 
    //$redirectUri);
    
    //Redirect the user to the authorization end point
    //with the Loep end point
    return new \GooglePlus\AuthorizationEndPoint\LoepGenerator($app);
});

$app['oauth2.redirectionEndPoint.handler'] = $app->share(function() use ($app) {
    /**
     * Use default handler of the request
     */
    return new \GooglePlus\RedirectionEndPoint\DefaultHandler($app);

    /**
     * Use the FKooman library
     */
    //return new \GooglePlus\RedirectionEndPoint\FKoomanRedirectionHandler($app);
    
   
    /**
     * Use the novakov redirection handler
     */
    /*
    return new \GooglePlus\RedirectionEndPoint\NovakovRedirectionHandler($app);
    */
   
   /**
    * Use the ADOY redirection handler
    */
   /*
   return new \GooglePlus\RedirectionEndPoint\AdoyRedirectionHandler($app);
   */
  
  /**
   * Use the Loep Redirection handler
   */
  //return new \GooglePlus\RedirectionEndPoint\LoepRedirectionHandler($app);
});

$app['oauth2.token.revoker'] = $app->share(function() use ($app) {
    //use default implementation
    return new \GooglePlus\TokenRevoker\DefaultTokenRevoker();

    //FKooman token revoker
    /*
    return (new \GooglePlus\TokenRevoker\FKoomanTokenRevoker($app));
    */
});

return $app;