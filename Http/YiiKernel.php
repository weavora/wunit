<?php

namespace WUnit\Http;

use WUnit\HttpKernel\HttpKernelInterface;
use WUnit\HttpFoundation\Request;
use WUnit\HttpFoundation\Response;
 
class YiiKernel implements HttpKernelInterface
{
	
	public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
	{
		
		$app = $this->createYiiApp();
		$app->setComponent('request',new YiiRequest());
		$app->request->inject($request->query->all(), $request->request->all(), $request->server->all());
//		$app->attachEventHandler('onEndRequest', function($event) {
//
//		});

		$statusCode = 200;
		

		ob_start();
		try {
			$app->processRequest();
		} catch (Exception $e) {
			$statusCode = 404;
		}
		
		$content = ob_get_contents();
		ob_end_clean();

		return new Response($content, $statusCode, headers_list());
	}

	/**
	 * @return \CWebApplication
	 */
	protected function createYiiApp()
	{
		return \Yii::app();
//		$config = require(\Yii::getPathOfAlias('application.config').'/test.php');
//		return \Yii::createWebApplication($config);
	}
}
