<?php

include_once(__DIR__.'/include/bootstrap.php');

include(__DIR__.'/include/webBootstrap.php');

$app = require(__DIR__.'/include/app.php');

require(__DIR__.'/include/controllers.php');

$app->run();