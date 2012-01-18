<?php


error_reporting( E_ALL | E_STRICT );

require(dirname(__FILE__).'/../../../../../framework/yiit.php');
require(dirname(__FILE__) . '/../WUnit.php');
require(dirname(__FILE__) . '/../PHPUnit/ResultPrinter.php');

//require(dirname(__FILE__) . '/fixtures/TestController.php');

//Yii::createWebApplication(array(
//	'basePath' => dirname(__FILE__).'/',
//	'components' => array(
//		'urlManager' => array(
//			'urlFormat' => 'path',
////			'useStrictParsing' => false,
//			'rules'=>array(
//				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
//				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
//				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
//			),
//		),
//	),
//));
//
//Yii::app()->controllerMap['test'] = 'TestController';

Yii::createWebApplication(require(dirname(__FILE__) . '/fixtures/config/main.php'));