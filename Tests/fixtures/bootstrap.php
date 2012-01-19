<?php

error_reporting( E_ALL | E_STRICT );

require(dirname(__FILE__).'/../../../../../../framework/yiit.php');

Yii::createWebApplication(require(dirname(__FILE__) . '/config/main.php'));