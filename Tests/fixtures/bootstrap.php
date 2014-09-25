<?php

error_reporting( E_ALL | E_STRICT );

require(dirname(__FILE__) .'/../../vendor/yiisoft/yii/framework/yiit.php');
require(dirname(__FILE__) . '/../../WUnit.php');

WUnit::createWebApplication(require(dirname(__FILE__) . '/config/main.php'));
