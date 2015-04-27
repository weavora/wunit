<?php

$loader = require(dirname(__FILE__) .'/../vendor/autoload.php');
error_reporting( E_ALL | E_STRICT );
$yiit = dirname(__FILE__) .'/../vendor/yiisoft/yii/framework/yiit.php';
$config = dirname(__FILE__) . '/fixtures/config/main.php';

require_once($yiit);

WUnit::createWebApplication($config);
