<?php

include_once(__DIR__.'/../include/bootstrap.php');

$app = require(ROOT_PATH.'/include/app.php');

require(ROOT_PATH.'/include/controllers.php');

$app->run();