<?php
/**
 * Set the error reporting and the time zone
 */
date_default_timezone_set('America/Argentina/Buenos_Aires');


$debug = true;
if ($debug) {
    $values = array(
        'error_reporting'=>E_ALL,
        'display_errors'=>"1",
        'display_startup_errors'=>"1",
        'log_errors'=>"1",
        'error_log'=>"/home/user/tmp/php/errors/error.log"
    );
    foreach ($values as $key => $value) {
        ini_set($key, $value);
    }
}

define('ROOT_PATH', realpath(__DIR__.'/..'));

/*
Setup log4php
*/
include_once ROOT_PATH . '/vendor/apache/log4php/src/main/php/Logger.php';
\Logger::configure(ROOT_PATH . '/config/log4php.ini');

$loader = require_once(__DIR__.'/../vendor/autoload.php');

